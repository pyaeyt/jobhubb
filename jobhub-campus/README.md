# InternHub

A university internship office for enrolled students. One registrar posts every opening. Students apply with a letter and a résumé, then follow the decision on their own account. Host organizations do not sign in.

The older job board in the parent folder is the PHP and MySQL site. InternHub is a separate JavaScript application.

## Database

Use **SQLite**.

The records are ordinary tables — students, hosts, internships, applications — so a relational database is the right kind. The office has one registrar and a campus of students, not a queue of employers. SQLite keeps that database in one file, `data/jobhub.db`. There is no MySQL service to start, and a backup is a copy of the file.

Node.js 22.13 and newer include SQLite, so this project uses the built-in `node:sqlite` module. Nothing extra is installed for the database.

If several offices later share one server, the same tables can be moved to MySQL or PostgreSQL. Until then, SQLite is the simpler and safer choice.

To start again from the sample board, stop the app and delete `data/jobhub.db`. The next start recreates the file, the registrar, two sample students, and the sample internships.

## Run

```bash
cd jobhub-campus
npm install
npm start
```

Open [http://localhost:3000](http://localhost:3000).

Apache / XAMPP is not required. This app is Node.js, not PHP.

## Sample accounts

| Who | Email | Password |
| --- | --- | --- |
| Registrar (the only admin) | admin@jobhub.example | admin123 |
| Student | elena.voss@student.example | student123 |
| Student | james.okonkwo@student.example | student123 |

Change the registrar password from Profile before any real use. Registration always creates a student. The site has no form for a second admin.

Résumés are stored in `data/resumes/` and can be downloaded only by the student who sent them, or by the registrar.
