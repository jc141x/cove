FROM docker.io/bitnami/symfony:4.4

# Install nodejs from nodesource
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get update -y
RUN apt-get install -y nodejs
RUN npm install -g yarn
RUN yarn install