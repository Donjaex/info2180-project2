-- Drop existing tables if they exist (optional, to start fresh)
DROP TABLE IF EXISTS Notes;
DROP TABLE IF EXISTS Contacts;

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

-- Insert sample data into the Contacts table
INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at)
VALUES 
('Mr.', 'John', 'Doe', 'john.doe@example.com', '555-1234', 'Example Inc.', 'Sales Lead', 1, 2, NOW(), NOW()),
('Ms.', 'Jane', 'Smith', 'jane.smith@example.com', '555-5678', 'Tech Corp.', 'Support', 2, 1, NOW(), NOW());

-- Insert sample data into the Notes table
INSERT INTO Notes (contact_id, comment, created_by, created_at)
VALUES 
(1, 'Followed up with John regarding product pricing.', 2, NOW()),
(2, 'Provided support to Jane for technical issues.', 1, NOW());

-- Retrieve all contacts
SELECT * FROM Contacts;

-- Retrieve all notes for a specific contact
SELECT * FROM Notes WHERE contact_id = 1;

-- Retrieve all contacts assigned to a specific user
SELECT * FROM Contacts WHERE assigned_to = 1;

-- Update contact information
UPDATE Contacts 
SET telephone = '555-9999', updated_at = NOW() 
WHERE id = 1;

-- Delete a contact and cascade delete its notes
DELETE FROM Contacts WHERE id = 2;

-- Count the number of notes for each contact
SELECT contact_id, COUNT(*) AS note_count 
FROM Notes 
GROUP BY contact_id;

-- Add unique constraint on email in the Contacts table (optional)
ALTER TABLE Contacts ADD CONSTRAINT unique_email UNIQUE (email);
