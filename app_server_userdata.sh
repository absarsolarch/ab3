#!/bin/bash
# App Server User Data Script

# Install required packages
yum update -y
yum install -y git httpd php php-pdo php-pgsql php-json

# Start and enable Apache
systemctl start httpd
systemctl enable httpd

# Clone the repository directly to the document root
cd /var/www/html
git clone https://github.com/absarsolarch/ab3.git .

# Set proper permissions
chown -R apache:apache /var/www/html
chmod -R 755 /var/www/html

# Get DB configuration from SSM Parameter Store
# Replace these with your actual parameter names
DB_HOST=$(aws ssm get-parameter --name "/ab3/db/host" --with-decryption --query "Parameter.Value" --output text)
DB_NAME=$(aws ssm get-parameter --name "/ab3/db/name" --with-decryption --query "Parameter.Value" --output text)
DB_USER=$(aws ssm get-parameter --name "/ab3/db/user" --with-decryption --query "Parameter.Value" --output text)
DB_PASSWORD=$(aws ssm get-parameter --name "/ab3/db/password" --with-decryption --query "Parameter.Value" --output text)

# Update the database configuration in backend.php
sed -i "s/\$host = \"YOUR_RDS_ENDPOINT\"/\$host = \"$DB_HOST\"/" /var/www/html/backend.php
sed -i "s/\$dbname = \"myappdb\"/\$dbname = \"$DB_NAME\"/" /var/www/html/backend.php
sed -i "s/\$user = \"YOUR_DB_USER\"/\$user = \"$DB_USER\"/" /var/www/html/backend.php
sed -i "s/\$password = \"YOUR_DB_PASSWORD\"/\$password = \"$DB_PASSWORD\"/" /var/www/html/backend.php

# Configure Apache to allow .htaccess files
sed -i '/<Directory "\/var\/www\/html">/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/httpd/conf/httpd.conf

# Restart Apache to apply changes
systemctl restart httpd

# Create a health check file
echo "OK" > /var/www/html/health.html

# Log completion
echo "App server setup completed" > /var/log/user-data-success.log
