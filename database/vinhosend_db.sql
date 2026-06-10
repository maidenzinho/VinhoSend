CREATE DATABASE IF NOT EXISTS vinhosend_ra2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vinhosend_ra2;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  tentativas_login INT NOT NULL DEFAULT 0,
  bloqueado_ate DATETIME NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vinhos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nome VARCHAR(120) NOT NULL,
  tipo VARCHAR(60) NOT NULL,
  pais VARCHAR(80) NOT NULL,
  safra INT NOT NULL,
  nota DECIMAL(3,1) NOT NULL,
  descricao TEXT NULL,
  imagem VARCHAR(255) NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL,
  CONSTRAINT fk_vinhos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS auditoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  acao VARCHAR(80) NOT NULL,
  detalhes VARCHAR(255) NULL,
  ip VARCHAR(45) NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_auditoria_usuario (usuario_id),
  CONSTRAINT fk_auditoria_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS anuncios_vinhos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vinho_id INT NOT NULL,
  vendedor_id INT NOT NULL,
  titulo VARCHAR(140) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  quantidade INT NOT NULL DEFAULT 1,
  status ENUM('ativo','pausado','vendido') NOT NULL DEFAULT 'ativo',
  observacoes TEXT NULL,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL,
  INDEX idx_anuncios_status (status),
  INDEX idx_anuncios_vendedor (vendedor_id),
  CONSTRAINT fk_anuncios_vinhos FOREIGN KEY (vinho_id) REFERENCES vinhos(id) ON DELETE CASCADE,
  CONSTRAINT fk_anuncios_vendedor FOREIGN KEY (vendedor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT chk_anuncios_preco CHECK (preco > 0),
  CONSTRAINT chk_anuncios_quantidade CHECK (quantidade >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS compras (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anuncio_id INT NOT NULL,
  comprador_id INT NOT NULL,
  vendedor_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  endereco_entrega TEXT NOT NULL,
  status ENUM('reservada','cancelada','concluida') NOT NULL DEFAULT 'reservada',
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_compras_comprador (comprador_id),
  INDEX idx_compras_vendedor (vendedor_id),
  CONSTRAINT fk_compras_anuncio FOREIGN KEY (anuncio_id) REFERENCES anuncios_vinhos(id),
  CONSTRAINT fk_compras_comprador FOREIGN KEY (comprador_id) REFERENCES usuarios(id),
  CONSTRAINT fk_compras_vendedor FOREIGN KEY (vendedor_id) REFERENCES usuarios(id),
  CONSTRAINT chk_compras_quantidade CHECK (quantidade > 0),
  CONSTRAINT chk_compras_total CHECK (total > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
