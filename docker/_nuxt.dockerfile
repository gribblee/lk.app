FROM node:14.13.1

ENV APP_ROOT /var/www/frontend
ENV HOST 0.0.0.0

#RUN addgroup -g 1000 node && adduser -G node -g node -s /bin/sh -D node
RUN mkdir -p ${APP_ROOT}
#RUN chown node:node /var/www/frontend

WORKDIR ${APP_ROOT}

ADD ./nuxt ${APP_ROOT}

RUN yarn install
# RUN yarn run generate
RUN yarn run build

CMD ["node"]