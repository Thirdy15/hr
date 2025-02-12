CREATE TABLE employee_evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluator_id INT NOT NULL,
    employee_id INT NOT NULL,
    category VARCHAR(255) NOT NULL,
    question VARCHAR(255) NOT NULL,
    rating INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evaluator_id) REFERENCES employee_register(e_id),
    FOREIGN KEY (employee_id) REFERENCES employee_register(e_id)
);
