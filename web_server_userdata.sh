#!/bin/bash
# Web Server User Data Script

# Install required packages
yum update -y
yum install -y git httpd php php-pdo php-json

# Start and enable Apache
systemctl start httpd
systemctl enable httpd

# Clone the repository directly to the document root
cd /var/www/html
git clone https://github.com/absarsolarch/ab3.git .

# Set proper permissions
chown -R apache:apache /var/www/html
chmod -R 755 /var/www/html

# Configure Apache to allow .htaccess files
sed -i '/<Directory "\/var\/www\/html">/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/httpd/conf/httpd.conf

# Restart Apache to apply changes
systemctl restart httpd

# Create a health check file
echo "OK" > /var/www/html/health.html

# Log completion
echo "Web server setup completed" > /var/log/user-data-success.log
