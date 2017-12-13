FROM node:6.7.0
RUN npm install -g yarn

WORKDIR /
COPY . .

RUN yarn install && yarn global add grunt-cli
