-- Tabla de roles
CREATE TABLE roles (
    id_rol SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES roles (id_rol)
);

-- Insertar roles iniciales
INSERT INTO roles (nombre) VALUES ('admin'), ('doctor'), ('paciente');

-- Usuario administrador por defecto
INSERT INTO usuarios (nombre, email, password, id_rol)
VALUES (
    'Administrador',
    'admin@clinica.com',
    -- Contraseña encriptada: admin123
    crypt('admin123', gen_salt('bf')),
    1
);
