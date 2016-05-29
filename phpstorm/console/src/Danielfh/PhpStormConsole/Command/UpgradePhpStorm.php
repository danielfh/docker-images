<?php

namespace Danielfh\PhpStormConsole\Command;

use Danielfh\PhpStormConsole\Helper\Downloader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpgradePhpStorm extends Command
{
    const URL_JETBRAINS_RELEASE = 'https://data.services.jetbrains.com/products/releases?code=PS&latest=true&type=release&_=%s';

    const BASE_PATH = '/opt/danielfh/phpstorm';
    const TEMP_PATH = self::BASE_PATH . '/tmp';
    const APPLICATION_PATH = self::BASE_PATH . '/src';

    protected function configure()
    {
        $this->setName('upgrade');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initEnvironment();

        $symfonyStyle = new SymfonyStyle($input, $output);
        $symfonyStyle->title('Upgrade PhpStorm');

        $symfonyStyle->comment('Fetching data about the current release...');
        $lastRelease = $this->fetchReleaseData($symfonyStyle);

        $currentChecksum = $this->getCurrentChecksum();
        $symfonyStyle->comment('Fetching checksum...');
        $lastReleaseChecksum = $this->fetchChecksumData($lastRelease['download']['checksumLink'], $symfonyStyle);

        if (!$this->isNewRelease($currentChecksum, $lastReleaseChecksum)) {
            $symfonyStyle->block('Everything up to date');
            exit(0);
        }

        $symfonyStyle->comment("Downloading build {$lastRelease['build']}...");
        $phpStormTempFile = $this->downloadToTempFile($lastRelease['download']['link'], $symfonyStyle);

        $this->install($phpStormTempFile, $lastReleaseChecksum, $symfonyStyle);
        $symfonyStyle->comment("DONE!");
    }

    protected function isNewRelease($currentChecksum, $lastReleaseChecksum)
    {
        return $currentChecksum != $lastReleaseChecksum;
    }

    protected function getCurrentChecksum()
    {
        $checksum = false;

        if (file_exists(self::BASE_PATH . '/checksum')) {
            $checksum = file_get_contents(self::BASE_PATH . '/checksum');
        }

        return $checksum;
    }

    protected function initEnvironment()
    {
        if (!file_exists(self::BASE_PATH)){
            mkdir(self::BASE_PATH);
        }

        if (!file_exists(self::APPLICATION_PATH)){
            mkdir(self::APPLICATION_PATH);
        }
    }

    protected function install(string $file, $checksum, SymfonyStyle $symfonyStyle)
    {
        $symfonyStyle->comment("Extracting data...");
        $path = $this->extractData($file);

        unlink($file);

        $pathData = scandir($path);
        $phpStormSrc = $pathData[2];

        $appFilesPath = self::APPLICATION_PATH . '/*';
        exec("rm -rf {$appFilesPath}");
        $target = self::APPLICATION_PATH;

        exec("cp -rf {$path}/$phpStormSrc/* {$target}/");

        file_put_contents(self::BASE_PATH . '/checksum', $checksum);
    }

    protected function extractData(string $file)
    {
        $tempPath = tempnam(self::TEMP_PATH, 'phpStorm.');
        unlink($tempPath);
        mkdir($tempPath);

        $output = [];
        exec("tar -zxvf {$file} -C {$tempPath}", $output, $status);

        if ($status !== 0) {
            throw new \Exception('Error while extracting data...');
        }

        return $tempPath;
    }

    protected function downloadToTempFile(string $url, SymfonyStyle $symfonyStyle)
    {
        $tempFile = tempnam(self::TEMP_PATH, 'phpStorm.');
        $tempFile .= '.tar.gz';

        $progressBar = $symfonyStyle->createProgressBar();
        $result = Downloader::saveContent($url, $tempFile, $progressBar);
        $symfonyStyle->newLine(2);

        if (!$result) {
            $symfonyStyle->error("Error while downloading {$url}");
            exit (-1);
        }

        return $tempFile;
    }

    protected function fetchChecksumData (string $checksumUrl, SymfonyStyle $symfonyStyle)
    {
        $progressBar = $symfonyStyle->createProgressBar();
        $checksum = Downloader::getContent($checksumUrl, $progressBar, true);
        $symfonyStyle->newLine(2);

        if ($checksum) {
            $checksumData = explode(' ', $checksum);
            $checksum = reset($checksumData);
        }

        return $checksum;
    }

    protected function fetchReleaseData (SymfonyStyle $symfonyStyle)
    {
        $urlRelease = $this->getUrlRelease();

        $progressBar = $symfonyStyle->createProgressBar();
        $lastReleaseDataRaw = Downloader::getContent($urlRelease, $progressBar);
        $symfonyStyle->newLine(2);

        $lastReleaseJson = json_decode($lastReleaseDataRaw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $symfonyStyle->error('Bad response');
            exit (-1);
        }

        $lastRelease = $this->extractReleaseData($lastReleaseJson);

        return $lastRelease;
    }

    protected function getUrlRelease()
    {
        $url = sprintf(self::URL_JETBRAINS_RELEASE, time() * 1000);
        return $url;
    }

    protected function extractReleaseData(array $release)
    {
        if (!isset($release['PS']) || !isset($release['PS'][0]) ) {
            throw new \Exception('Error extracting release data.');
        }

        $result = [
            'build'    => $release['PS'][0]['build'],
            'date'     => $release['PS'][0]['date'],
            'download' => [
                'link'         => $release['PS'][0]['downloads']['linux']['link'],
                'size'         => $release['PS'][0]['downloads']['linux']['size'],
                'checksumLink' => $release['PS'][0]['downloads']['linux']['checksumLink'],
            ],
        ];

        return $result;
    }
}