FROM node:14-alpine as frontend
WORKDIR /
COPY frontend/package*.json ./app/

RUN npm --prefix app install
COPY frontend app
RUN npm run --prefix app build --prod


FROM maven:3.6.0-jdk-8-slim AS build

WORKDIR /

COPY backend/src /home/app/src
COPY backend/pom.xml /home/app
COPY --from=frontend backend/src/main/resources/static /home/app/src/main/resources/static
RUN mvn -f /home/app/pom.xml clean package

FROM openjdk:8-jre-slim
COPY --from=build /home/app/target/backend.jar /usr/local/lib/backend.jar
# COPY .env .env

EXPOSE 1337
ENTRYPOINT ["java","-jar","/usr/local/lib/backend.jar", "start"]