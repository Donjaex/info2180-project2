-- Drop existing tables if they exist (optional)
DROP TABLE IF EXISTS Notes;
DROP TABLE IF EXISTS Contacts;
DROP TABLE IF EXISTS Users;

-- Create the Users table
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    password VARCHAR(255), -- Ensure hashed passwords
    email VARCHAR(255) UNIQUE,
    role VARCHAR(255),
    created_at DATETIME
);

-- Insert a user with a hashed password
-- Assuming you are hashing the password "password123" in your application
INSERT INTO Users (firstname, lastname, password, email, role, created_at) 
VALUES ('Admin', 'User', '$2a$12$62yM./CHHJn56SEHRWb4TuZfpSWAklSJiQ7H1BakorNYESiqllDnm', 'admin@project2.com', 'admin', NOW());

-- Create the Contacts table
CREATE TABLE Contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    email VARCHAR(255),
    telephone VARCHAR(20),
    company VARCHAR(255),
    type VARCHAR(50), -- e.g., Sales Lead or Support
    assigned_to INT, -- Links to the id of a user
    created_by INT, -- Links to the id of a user
    created_at DATETIME,
    updated_at DATETIME
);

-- Create the Notes table
CREATE TABLE Notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT, -- Links to the id of a contact
    comment TEXT,
    created_by INT, -- Links to the id of a user
    created_at DATETIME,
    FOREIGN KEY (contact_id) REFERENCES Contacts(id) ON DELETE CASCADE
);
