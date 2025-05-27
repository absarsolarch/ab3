#!/bin/bash
# DB Server User Data Script

# Install PostgreSQL
yum update -y
amazon-linux-extras install postgresql13 -y
yum install -y postgresql postgresql-server postgresql-devel postgresql-contrib

# Initialize the database
postgresql-setup initdb

# Start and enable PostgreSQL
systemctl start postgresql
systemctl enable postgresql

# Clone the repository to get the DB setup script
yum install -y git
git clone https://github.com/absarsolarch/ab3.git /tmp/ab3

# Get DB configuration from SSM Parameter Store
# Replace these with your actual parameter names
DB_NAME=$(aws ssm get-parameter --name "/ab3/db/name" --with-decryption --query "Parameter.Value" --output text)
DB_USER=$(aws ssm get-parameter --name "/ab3/db/user" --with-decryption --query "Parameter.Value" --output text)
DB_PASSWORD=$(aws ssm get-parameter --name "/ab3/db/password" --with-decryption --query "Parameter.Value" --output text)

# Create database and user
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME;"
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"

# Run the setup script
sudo -u postgres psql -d $DB_NAME -f /tmp/ab3/db_setup.sql

# Configure PostgreSQL to allow connections from app servers
# Update pg_hba.conf to allow connections from the app server subnet
echo "host    all             all             10.0.0.0/16            md5" >> /var/lib/pgsql/data/pg_hba.conf

# Update postgresql.conf to listen on all interfaces
sed -i "s/#listen_addresses = 'localhost'/listen_addresses = '*'/" /var/lib/pgsql/data/postgresql.conf

# Restart PostgreSQL to apply changes
systemctl restart postgresql

# Log completion
echo "Database server setup completed" > /var/log/user-data-success.log
