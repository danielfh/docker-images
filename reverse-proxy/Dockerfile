FROM splazit/privoxy-alpine

RUN apk add openssh-client

RUN mkdir -p /opt && touch /opt/ssh_tunnel && chmod +x /opt/ssh_tunnel

WORKDIR /opt

ENTRYPOINT ["/opt/ssh_tunnel"]

RUN echo $'#!/bin/sh\n\
cp /dev/stdin /tmp/ssh_key.pem\n\
chmod 600 /tmp/ssh_key.pem\n\
cat /tmp/ssh_key.pem\n\
echo "[STARTING SSH TUNNEL]"\n\
ssh -o "StrictHostKeyChecking no" -N -f -i /tmp/ssh_key.pem -R ${2:"localhost:8118:localhost:8118"} $1\n\
echo "[STARTING PRIVOXY]"\n\
privoxy --no-daemon --user privoxy /etc/privoxy/config\n'\
>> /opt/ssh_tunnel
