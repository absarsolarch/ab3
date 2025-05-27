-- Database setup script for the property management application

-- Create database if it doesn't exist
-- Note: This needs to be run as a user with permission to create databases
CREATE DATABASE IF NOT EXISTS myappdb;

-- Connect to the database
\c myappdb

-- Create properties table
CREATE TABLE IF NOT EXISTS properties (
    id SERIAL PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    property_type VARCHAR(50) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    size_sqft INTEGER NOT NULL,
    bedrooms INTEGER,
    bathrooms INTEGER,
    location VARCHAR(200) NOT NULL,
    status VARCHAR(50) DEFAULT 'Available',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create a sample property
INSERT INTO properties (title, property_type, price, size_sqft, bedrooms, bathrooms, location, status, description)
SELECT 'Sample Property', 'Apartment', 450000, 1200, 3, 2, 'Kuala Lumpur', 'Available', 'This is a sample property.'
WHERE NOT EXISTS (SELECT 1 FROM properties LIMIT 1);

-- Create application user and grant permissions
-- Replace 'app_user' and 'app_password' with your actual values
-- DO $$
-- BEGIN
--     IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'app_user') THEN
--         CREATE USER app_user WITH PASSWORD 'app_password';
--     END IF;
-- END
-- $$;

-- GRANT ALL PRIVILEGES ON DATABASE myappdb TO app_user;
-- GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO app_user;
-- GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO app_user;
