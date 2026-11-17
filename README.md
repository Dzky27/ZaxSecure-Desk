# 🛡️ ZaxSecure Desk V2

### Cybersecurity Incident Management & Response Platform

<p align="center">
  **A centralized platform for managing, monitoring, and responding to cybersecurity incidents.**
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Project-ZaxSecure%20Desk-B11217?style=for-the-badge&logo=shield&logoColor=white" />
  <img src="https://img.shields.io/badge/Framework-Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Language-PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Status-Academic%20Project-2563EB?style=for-the-badge" />
</p>

---

## Overview

**ZaxSecure Desk V2** is a web-based cybersecurity incident management platform designed to support structured incident reporting, threat classification, investigation, evidence management, and incident response coordination.

The platform provides a centralized workspace for security administrators and incident reporters to manage security events through an organized workflow.

Built with a focus on usability, access control, and structured incident documentation, ZaxSecure Desk aims to demonstrate how web technologies can support practical cybersecurity operations.

## Core Capabilities

| Module | Description |
|---|---|
| Incident Management | Create, monitor, and manage cybersecurity incident reports. |
| Threat Classification | Organize incidents according to severity and priority. |
| Investigation Workflow | Track investigation progress and incident updates. |
| Evidence Management | Manage supporting digital evidence and attachments. |
| Audit Trail | Maintain records of relevant user activities and changes. |
| Role-Based Access | Separate administrative and reporter access. |
| Dashboard & Reports | Present incident information through centralized monitoring views. |

*Feature descriptions should reflect the functionality actually implemented in the deployed application.*

## Technology Stack

The project uses the following technologies, subject to verification against the actual source code:

- **Backend:** Laravel / PHP
- **Database:** MySQL / MariaDB
- **Frontend:** Blade, Tailwind CSS
- **Build Tools:** Vite
- **Client-side:** JavaScript
- **Visualization:** Chart.js
- **Icons:** Lucide

## Application Architecture

ZaxSecure Desk follows Laravel's application structure, separating responsibilities into several components:

```text
ZaxSecure-Desk/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Models/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── artisan
├── composer.json
└── README.md
```

## Security Considerations

Security is an important part of the application's design and development process.

The project considers the following areas:

- Authentication and authorization.
- Role-based access restrictions.
- Input validation and request protection.
- Secure handling of application configuration.
- Protection of sensitive application files.
- Digital evidence access restrictions.
- Security testing against common web application vulnerabilities.

The actual implementation and testing status of each control must be verified against the application.

## Live Application

🌐 **Production URL:**  
https://zaxsecure.page.gd

The application is deployed using InfinityFree hosting for academic demonstration purposes.

## Repository

💻 **Source Code:**  
This repository contains the application source code and relevant project documentation.

## Academic Project

Developed as part of the **Web Application Development and Publication (Cyber Security)** coursework.

**Developer:** Muhammad Dzaky Putra Pamungkas  
**Student ID:** 20240810109  
**Class:** TINFC-2024-04  
**Program:** Informatics Engineering  
**Institution:** Universitas Kuningan

## Disclaimer

ZaxSecure Desk is an academic project developed for educational purposes and cybersecurity application development practice.

This application is intended for authorized use, testing, and demonstration in controlled environments. Do not use it to access, monitor, or manipulate systems or data without proper authorization.

---

<p align="center">
  **ZaxSecure Desk V2**
  <br />
  <sub>Structured Incident Management. Better Security Awareness.</sub>
</p>
