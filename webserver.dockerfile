FROM nginx:latest
ADD ./docker/nginx/conf.d/app.conf /etc/nginx/conf.d/default.conf
#COPY ./nginx/ssl/ /etc/nginx/ssl/
