FROM nginx:1.18.0-alpine

LABEL maintainer="umarbatalov@gmail.com"

WORKDIR /srv

ADD docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
