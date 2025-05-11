🌿 Phytosense: Crop Disease Diagnostic Platform

Phytosense is a real-time crop disease diagnostic web platform that allows users to scan plant leaves using a camera or upload images to identify possible diseases. It provides diagnostic results, confidence levels, and treatment recommendations to help Filipino farmers and gardeners take timely action.

---
📂 Repository

🔗 GitHub Repo: [https://github.com/powiepow/Phytosense.git](https://github.com/powiepow/Phytosense.git)

---
⚙️ Features

- Real-time disease detection via webcam or image upload  
- Diagnosis confidence level (%)  
- Detailed treatment and prevention info  
- Responsive user interface  
- Community forum 

---
🛠️ Installation & Setup Guide
1. Clone the Repository

*bash
git clone https://github.com/powiepow/Phytosense.git
cd Phytosense

2. Set Up XAMPP (PHP + MySQL)
- Download and install XAMPP
- Launch Apache and MySQL
- Move the Phytosense project folder into the htdocs/ directory inside your XAMPP installation

3. Create & Import the Database
- Go to http://localhost/phpmyadmin
- Create a new database named:  phytosensedb
- Import the phytosensedb.sql file located in the project folder

4. Run the Web App
- In your browser, go to:    http://localhost/Phytosense/

You can now:
- Scan crop leaves
- View diagnosis and confidence level
- Read disease info and prevention tips
- Interact in community forum

---

🚀 Deployment (Live Server)
To deploy Phytosense online:
1. Upload all files to your hosting provider (must support PHP & MySQL)
2. Import the phytosensedb.sql file into your host’s phpMyAdmin
3. Update your database credentials in the config file (e.g., db.php)
4. Access the app via your live domain or subdomain

