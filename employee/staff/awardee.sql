CREATE TABLE employee_register (
    e_id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(50),
    middlename VARCHAR(50),
    lastname VARCHAR(50),
    birthdate DATE,
    email VARCHAR(100),
    role VARCHAR(50),
    position VARCHAR(50),
    department VARCHAR(50),
    phone_number VARCHAR(20),
    address VARCHAR(255),
    pfp VARCHAR(255)
);

CREATE TABLE admin_evaluations (
    eval_id INT PRIMARY KEY AUTO_INCREMENT,
    e_id INT,
    quality FLOAT,
    communication_skills FLOAT,
    teamwork FLOAT,
    punctuality FLOAT,
    initiative FLOAT,
    FOREIGN KEY (e_id) REFERENCES employee_register(e_id)
);

-- Insert sample data into employee_register
INSERT INTO employee_register (firstname, middlename, lastname, birthdate, email, role, position, department, phone_number, address, pfp)
VALUES 
('John', 'A', 'Doe', '1985-01-01', 'john.doe@example.com', 'Employee', 'Developer', 'IT', '1234567890', '123 Main St', 'path/to/pfp1.jpg'),
('Jane', 'B', 'Smith', '1990-02-02', 'jane.smith@example.com', 'Employee', 'Designer', 'Design', '0987654321', '456 Elm St', 'path/to/pfp2.jpg');

-- Insert sample data into admin_evaluations
INSERT INTO admin_evaluations (e_id, quality, communication_skills, teamwork, punctuality, initiative)
VALUES 
(1, 8.5, 9.0, 8.0, 9.5, 8.5),
(2, 9.0, 8.5, 9.5, 8.0, 9.0);
