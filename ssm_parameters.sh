#!/bin/bash
# Script to create SSM parameters for database configuration

# Replace these values with your actual database configuration
DB_HOST="your-db-endpoint.region.rds.amazonaws.com"
DB_NAME="myappdb"
DB_USER="app_user"
DB_PASSWORD="YourSecurePassword123!"

# Create SSM parameters
aws ssm put-parameter --name "/ab3/db/host" --value "$DB_HOST" --type "SecureString" --overwrite
aws ssm put-parameter --name "/ab3/db/name" --value "$DB_NAME" --type "SecureString" --overwrite
aws ssm put-parameter --name "/ab3/db/user" --value "$DB_USER" --type "SecureString" --overwrite
aws ssm put-parameter --name "/ab3/db/password" --value "$DB_PASSWORD" --type "SecureString" --overwrite

echo "SSM parameters created successfully"
