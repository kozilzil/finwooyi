FROM node:18-alpine
WORKDIR /app

# install deps
COPY package*.json ./
RUN npm install --production=false

# copy source
COPY . .

# build
RUN npm run build

# serve with a simple static server
RUN npm install -g serve
CMD ["serve", "-s", "dist", "-l", "3000"]
