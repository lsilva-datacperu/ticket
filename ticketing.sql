-- Usar base de datos
CREATE DATABASE IF NOT EXISTS ticketing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ticketing;

-- Usuarios
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('solicitante','desarrollador','encargado','admin') NOT NULL DEFAULT 'solicitante',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Módulos / áreas del sistema
CREATE TABLE modules (
  id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tickets
CREATE TABLE tickets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE, -- ej: TKT-0001
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  request_type ENUM('mejora','bug','nueva_funcionalidad') NOT NULL,
  priority ENUM('baja','media','alta') NOT NULL DEFAULT 'media',
  status ENUM('abierto','en_progreso','resuelto','cerrado','rechazado') NOT NULL DEFAULT 'abierto',
  module_id SMALLINT UNSIGNED,
  requested_by INT UNSIGNED NOT NULL,
  assigned_to INT UNSIGNED, -- desarrollador asignado
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  target_date DATE DEFAULT NULL, -- opcional: fecha objetivo
  FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE SET NULL,
  FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE RESTRICT,
  FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Pasos para reproducir (ordenados)
CREATE TABLE ticket_steps (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT UNSIGNED NOT NULL,
  step_order INT UNSIGNED NOT NULL DEFAULT 1,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Adjuntos (capturas / video). Se recomienda guardar archivos en filesystem y ruta en BD.
CREATE TABLE attachments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT UNSIGNED NOT NULL,
  filename VARCHAR(255) NOT NULL,
  filepath VARCHAR(500) NOT NULL, -- ruta relativa en servidor
  mime_type VARCHAR(100),
  filesize INT UNSIGNED,
  uploaded_by INT UNSIGNED,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Comentarios / seguimiento conversacional
CREATE TABLE comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT UNSIGNED NOT NULL,
  author_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  is_internal TINYINT(1) NOT NULL DEFAULT 0, -- 1 = solo para desarrolladores/encargado
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Histórico de cambios (auditoría)
CREATE TABLE ticket_history (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT UNSIGNED NOT NULL,
  changed_by INT UNSIGNED,
  field_name VARCHAR(100), -- e.g., 'status', 'assigned_to', 'priority'
  old_value TEXT,
  new_value TEXT,
  comment TEXT, -- opcional texto que explique cambio
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX (ticket_id)
) ENGINE=InnoDB;

-- Tags y relación N-N
CREATE TABLE tags (
  id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE ticket_tags (
  ticket_id INT UNSIGNED NOT NULL,
  tag_id SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (ticket_id, tag_id),
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Índices útiles
CREATE INDEX idx_tickets_status_priority ON tickets(status, priority);
CREATE INDEX idx_tickets_assigned ON tickets(assigned_to);
CREATE INDEX idx_tickets_requester ON tickets(requested_by);
