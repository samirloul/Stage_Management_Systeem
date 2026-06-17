USE stage_management;

-- Stored procedures voor herbruikbare database-acties.
-- DROP PROCEDURE IF EXISTS voorkomt errors bij opnieuw importeren.

DROP PROCEDURE IF EXISTS sp_add_student;
DELIMITER $$
CREATE PROCEDURE sp_add_student(
    IN p_student_number VARCHAR(30),
    IN p_first_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_program VARCHAR(120),
    IN p_start_year SMALLINT,
    IN p_status VARCHAR(20)
)
BEGIN
    INSERT INTO students (student_number, first_name, last_name, email, program, start_year, status, created_at, updated_at)
    VALUES (p_student_number, p_first_name, p_last_name, p_email, p_program, p_start_year, p_status, NOW(), NOW());
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_assign_internship;
DELIMITER $$
CREATE PROCEDURE sp_assign_internship(
    IN p_student_id BIGINT,
    IN p_company_id BIGINT,
    IN p_title VARCHAR(180),
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_hours TINYINT
)
BEGIN
    INSERT INTO internships (student_id, company_id, title, start_date, end_date, hours_per_week, status, created_at, updated_at)
    VALUES (p_student_id, p_company_id, p_title, p_start_date, p_end_date, p_hours, 'planned', NOW(), NOW());
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS sp_dashboard_summary;
DELIMITER $$
CREATE PROCEDURE sp_dashboard_summary()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM students) AS total_students,
        (SELECT COUNT(*) FROM companies) AS total_companies,
        (SELECT COUNT(*) FROM internships WHERE status='active') AS active_internships,
        (SELECT ROUND(AVG(score), 1) FROM reviews) AS avg_review_score;
END $$
DELIMITER ;
