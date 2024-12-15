DROP TABLE IF EXISTS Notes;
DROP TABLE IF EXISTS Contacts;

CREATE TABLE Contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    email VARCHAR(255),
    telephone VARCHAR(20),
    company VARCHAR(255),
    type VARCHAR(50),
    assigned_to INT,
    created_by INT,
    created_at DATETIME,
    updated_at DATETIME
);

-- Create the Notes table
CREATE TABLE Notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT,
    comment TEXT,
    created_by INT,
    created_at DATETIME,
    FOREIGN KEY (contact_id) REFERENCES Contacts(id) ON DELETE CASCADE
);

INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at)
VALUES 
('Mr.', 'John', 'Doe', 'john.doe@example.com', '555-1234', 'Example Inc.', 'Sales Lead', 1, 2, NOW(), NOW()),
('Ms.', 'Jane', 'Smith', 'jane.smith@example.com', '555-5678', 'Tech Corp.', 'Support', 2, 1, NOW(), NOW());

INSERT INTO Notes (contact_id, comment, created_by, created_at)
VALUES 
(1, 'Followed up with John regarding product pricing.', 2, NOW()),
(2, 'Provided support to Jane for technical issues.', 1, NOW());

SELECT * FROM Contacts;

SELECT * FROM Notes WHERE contact_id = 1;

SELECT * FROM Contacts WHERE assigned_to = 1;

UPDATE Contacts 
SET telephone = '555-9999', updated_at = NOW() 
WHERE id = 1;

DELETE FROM Contacts WHERE id = 2;

SELECT contact_id, COUNT(*) AS note_count 
FROM Notes 
GROUP BY contact_id;

ALTER TABLE Contacts ADD CONSTRAINT unique_email UNIQUE (email);
