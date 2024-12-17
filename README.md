# m7blog

This project is a simple blog application built with PHP. It allows users to register, log in, create posts, and comment on them. The application uses a MySQL database for data storage and PDO for database interactions.

## Features

- User registration and login
- Create, read, and manage blog posts
- Comment on posts
- User profile management

## Technologies Used

- PHP
- MySQL
- PDO
- Tailwind CSS for styling

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd m7blog
   ```

3. Install the required npm packages:
   ```
   npm install
   ```

4. Set up the database:
   - Create a MySQL database named `blog_db`.
   - Import the SQL schema (if provided).

5. Update the database configuration in `app/config/Database.php` if necessary.

## Usage

- Start the PHP built-in server:
  ```
  php -S localhost:8000 -t app
  ```

- Open your browser and go to `http://localhost:8000`.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any improvements or bug fixes.

## License

This project is licensed under the ISC License.