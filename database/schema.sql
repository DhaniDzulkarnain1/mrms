-- Material Request Management System (MRMS) Database Schema
-- PostgreSQL Version

-- Custom types for ENUM replacement
CREATE TYPE user_role AS ENUM ('production', 'warehouse');
CREATE TYPE rfi_status AS ENUM ('draft', 'submitted', 'checked');
CREATE TYPE item_status AS ENUM ('pending', 'ready', 'not_ready');

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role user_role NOT NULL,
    is_active SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for users
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_role ON users(role);

-- RFI (Request for Item) table
CREATE TABLE IF NOT EXISTS rfi (
    id SERIAL PRIMARY KEY,
    rfi_number VARCHAR(50) NOT NULL UNIQUE,
    requester_id INTEGER NOT NULL,
    project_name VARCHAR(200) NOT NULL,
    status rfi_status NOT NULL DEFAULT 'draft',
    submitted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Create indexes for rfi
CREATE INDEX idx_rfi_number ON rfi(rfi_number);
CREATE INDEX idx_requester ON rfi(requester_id);
CREATE INDEX idx_status ON rfi(status);

-- RFI Items table
CREATE TABLE IF NOT EXISTS rfi_items (
    id SERIAL PRIMARY KEY,
    rfi_id INTEGER NOT NULL,
    material_name VARCHAR(200) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    uom VARCHAR(50) NOT NULL,
    status item_status DEFAULT 'pending',
    remark TEXT NULL,
    checked_by INTEGER NULL,
    checked_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rfi_id) REFERENCES rfi(id) ON DELETE CASCADE,
    FOREIGN KEY (checked_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for rfi_items
CREATE INDEX idx_rfi_id ON rfi_items(rfi_id);
CREATE INDEX idx_item_status ON rfi_items(status);

-- Function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Triggers for auto-updating updated_at
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_rfi_updated_at BEFORE UPDATE ON rfi
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_rfi_items_updated_at BEFORE UPDATE ON rfi_items
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Insert default users (password: password123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('production1', '$2y$10$BRLsbLf3q1gK9GwI0rD1.eyo6JFc2QuYJVatEIl4GhvrG368VmArS', 'Dhani Dzulkarnain', 'production@example.com', 'production'),
('warehouse1', '$2y$10$BRLsbLf3q1gK9GwI0rD1.eyo6JFc2QuYJVatEIl4GhvrG368VmArS', 'Dzulkarnain Dhani', 'warehouse@example.com', 'warehouse');

-- Sample data for testing
INSERT INTO rfi (rfi_number, requester_id, project_name, status) VALUES
('RFI-2026-0001', 1, 'Project Alpha Construction', 'submitted');

INSERT INTO rfi_items (rfi_id, material_name, quantity, uom, status) VALUES
(1, 'Plate 10 mm', 5, 'PCS', 'pending'),
(1, 'Pipe 4"', 10, 'MTR', 'pending'),
(1, 'Welding Rod', 20, 'BOX', 'pending'),
(1, 'Bolt M16', 50, 'PCS', 'pending');
