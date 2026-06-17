USE stage_management;

-- Demo data script.
-- Elke INSERT gebruikt WHERE NOT EXISTS, zodat je dit script meerdere keren kunt draaien.

INSERT INTO roles (name, label, created_at, updated_at)
SELECT 'admin', 'Administrator', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name = 'admin');

INSERT INTO roles (name, label, created_at, updated_at)
SELECT 'student', 'Student', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name = 'student');

INSERT INTO roles (name, label, created_at, updated_at)
SELECT 'company', 'Bedrijf', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE name = 'company');

INSERT INTO users (role_id, name, email, email_verified_at, password, created_at, updated_at)
SELECT (SELECT id FROM roles WHERE name='admin'), 'Stage Admin', 'admin@stagems.local', NOW(), '$2y$12$Qn6K7xQmyR0B/ojk5x1Fv.yQ95bPsGyjM3PoT7f4w0KQJQwOQJf9W', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@stagems.local');

INSERT INTO users (role_id, name, email, email_verified_at, password, created_at, updated_at)
SELECT (SELECT id FROM roles WHERE name='student'), 'Demo Student', 'student@stagems.local', NOW(), '$2y$12$Qn6K7xQmyR0B/ojk5x1Fv.yQ95bPsGyjM3PoT7f4w0KQJQwOQJf9W', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'student@stagems.local');

INSERT INTO users (role_id, name, email, email_verified_at, password, created_at, updated_at)
SELECT (SELECT id FROM roles WHERE name='company'), 'Demo Bedrijf', 'bedrijf@stagems.local', NOW(), '$2y$12$Qn6K7xQmyR0B/ojk5x1Fv.yQ95bPsGyjM3PoT7f4w0KQJQwOQJf9W', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'bedrijf@stagems.local');

INSERT INTO students (user_id, student_number, first_name, last_name, email, phone, program, start_year, status, created_at, updated_at)
SELECT (SELECT id FROM users WHERE email='student@stagems.local'), 'S10001', 'Daan', 'Jansen', 'daan.jansen@student.local', '0612345678', 'Software Development', 2024, 'active', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM students WHERE student_number = 'S10001');

INSERT INTO companies (user_id, name, contact_person, email, phone, city, industry, website, status, created_at, updated_at)
SELECT (SELECT id FROM users WHERE email='bedrijf@stagems.local'), 'Bright Future BV', 'Sanne de Vries', 'contact@brightfuture.local', '0201234567', 'Amsterdam', 'IT', 'https://brightfuture.example', 'active', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM companies WHERE name = 'Bright Future BV');

INSERT INTO internships (student_id, company_id, title, description, start_date, end_date, hours_per_week, mentor_name, status, created_at, updated_at)
SELECT (SELECT id FROM students WHERE student_number='S10001'), (SELECT id FROM companies WHERE name='Bright Future BV'), 'Backend Development Stage', 'Bouwen van API endpoints en dashboard features.', '2026-02-01', '2026-07-31', 32, 'Sanne de Vries', 'active', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM internships WHERE title = 'Backend Development Stage');

INSERT INTO reviews (internship_id, reviewer_user_id, score, feedback, review_date, recommendation, created_at, updated_at)
SELECT (SELECT id FROM internships WHERE title='Backend Development Stage'), (SELECT id FROM users WHERE email='admin@stagems.local'), 8, 'Goede voortgang en nette communicatie.', '2026-04-10', 'yes', NOW(), NOW()
WHERE NOT EXISTS (
		SELECT 1
		FROM reviews
		WHERE internship_id = (SELECT id FROM internships WHERE title='Backend Development Stage')
			AND review_date = '2026-04-10'
);
