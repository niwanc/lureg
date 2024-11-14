# Laravel API E-signature  with Docker, Swagger Documentation, and User Authentication

This is a Laravel-based API project that includes features for user authentication, document management, and e-signature functionality. The project is containerized using Docker for easy setup and deployment.

## Features

- **User Authentication**: Implemented with Laravel Passport or Sanctum for API authentication.
- **Document Management**: Upload documents (PDF format), store them securely, and manage the upload path.
- **Signature Requests**: Users can send signature requests to other registered users, track document statuses.
- **E-Signature**: Users can add a digital signature to documents, ensuring secure association of the signature with the document.
- **API Documentation**: Generated using Swagger, available for interaction and testing.
- **Debugging**: Laravel Telescope is included for real-time debugging.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Setting Up the Project](#setting-up-the-project)
- [Docker Configuration](#docker-configuration)
- [API Documentation with Swagger](#api-documentation-with-swagger)
- [Running the Project](#running-the-project)
- [Testing](#testing)

---

## Prerequisites

Before setting up the project, ensure that you have the following tools installed:

- **Docker**: [Install Docker](https://www.docker.com/get-started)
- **Docker Compose**: [Install Docker Compose](https://docs.docker.com/compose/install/)
- **PHP (8.2 or higher)**: If you plan to run the project locally outside of Docker

---

## Setting Up the Project

1. **Clone the repository**:

   ```bash
   git clone https://github.com/niwanc/lure.git
   cd lure

## Docker Configuration
This section provides instructions for running the Laravel API project using Docker and Docker Compose. It assumes that the necessary Docker setup files are already in place.
### Steps to Run the Application with Docker

1. **Starting Docker Containers**
    - Run the following command to start all necessary containers:
      ```bash
      docker-compose up -d
      ```

2. **Accessing the Project**
    - Once the containers are up, access the main project at:
      ```
      http://localhost:8097
      ```

# API Documentation and Monitoring in Laravel (Swagger & Telescope)

## Overview
This project includes **Swagger** for API documentation and **Laravel Telescope** for application monitoring. After setting up the Docker environment, follow the steps below to access Swagger documentation and Telescope in your browser.

### Accessing Swagger Documentation

1. **Swagger UI URL**
    - After the Docker containers are running, Swagger UI can be accessed at:
      ```
      http://localhost:8097/api/documentation
      ```
    - This URL displays the interactive Swagger interface, allowing you to explore and test API endpoints.

2. **Regenerating Swagger Docs (If Needed)**
    - If the API documentation needs to be updated, run the following command inside the Docker container:
      ```bash
      docker-compose exec app php artisan l5-swagger:generate OR
      php artisan l5-swagger:generate
      ```

### Accessing Laravel Telescope

1. **Telescope Dashboard URL**
    - Laravel Telescope can be accessed at:
      ```
      http://localhost:8097/telescope
      ```
    - Use this dashboard to monitor requests, exceptions, database queries, and other application metrics.

2. **Environment Requirements**
    - Telescope is configured to run in `local` and `staging` environments. Make sure your `.env` file specifies:
      ```env
      TELESCOPE_ENABLED=true
      APP_ENV=local
      ``
### Running Tests in Docker Environment

After setting up the Docker containers, you can run the Laravel test suite using the following command:

```bash
docker-compose exec app php artisan test
```
##Docker Configuration: 

Ensure the .env file is correctly copied and the environment variables are passed to the Docker container. In the docker-compose.yml, use:
```bash

docker-compose exec app php artisan passport:client --password
environment:
  - PASSPORT_PASSWORD_CLIENT_ID=${PASSPORT_PASSWORD_CLIENT_ID}
  - PASSPORT_PASSWORD_SECRET=${PASSPORT_PASSWORD_SECRET}
```

## CI/CD Pipeline for Laravel Vapor Deployment

## Overview
This project establishes a CI/CD pipeline for deploying to [Laravel Vapor](https://vapor.laravel.com/), with a setup that runs automated tests and prepares the application for deployment. The deployment step to Vapor is currently commented out, while all previous stages are functional.

### GitHub Workflows
The pipeline uses two GitHub workflows tailored to the `master` and `develop` branches:

- **Master Workflow:** Runs on pushes to the `master` branch, ensuring production-ready code goes through rigorous testing and build processes before deployment. The deployment step is currently commented out but will deploy to Vapor when re-enabled.

- **Develop Workflow:** Runs on pushes to the `develop` branch, focusing on pre-production checks and tests. This workflow allows the team to validate features and fixes in a staging environment.

## Pipeline Flow
1. **Code Linting & Formatting:** Ensures consistent code style across branches.
2. **Unit & Feature Testing:** Runs all unit and feature tests to validate functionality.
3. **Build Phase:** Prepares the application for deployment.
4. **Deployment to Vapor:** The deployment phase has been commented out temporarily, but the pipeline is fully operational through the testing phases.

