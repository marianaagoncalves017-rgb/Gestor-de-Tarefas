-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 16-Out-2026 às 12:12
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `11TGPSIBD_10`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `FEEDBACK`
--

CREATE TABLE `FEEDBACK` (
  `IDF` int(11) NOT NULL,
  `COMENTARIO` text NOT NULL,
  `DATA` datetime NOT NULL DEFAULT current_timestamp(),
  `IDP` int(11) NOT NULL,
  `IDProfessor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `PROJETO`
--

CREATE TABLE `PROJETO` (
  `IDP` int(11) NOT NULL,
  `TEMA` varchar(150) NOT NULL,
  `AREA` varchar(100) NOT NULL,
  `DATAFIM` date DEFAULT NULL,
  `IDAluno` int(11) NOT NULL,
  `IDProfessor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `PROJETO`
--

INSERT INTO `PROJETO` (`IDP`, `TEMA`, `AREA`, `DATAFIM`, `IDAluno`, `IDProfessor`) VALUES
(1, 'ProjectFlow - Gestão de PAP', 'Informática e Programação', '2027-06-30', 1, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `RECURSO`
--

CREATE TABLE `RECURSO` (
  `IDR` int(11) NOT NULL,
  `TITULO` varchar(100) NOT NULL,
  `DESCRICAO` text DEFAULT NULL,
  `LINK` varchar(255) NOT NULL,
  `DATA` datetime NOT NULL DEFAULT current_timestamp(),
  `IDP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `REGISTO`
--

CREATE TABLE `REGISTO` (
  `IDR` int(11) NOT NULL,
  `DESCRICAO` text NOT NULL,
  `DATA` datetime NOT NULL DEFAULT current_timestamp(),
  `IDP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `TAREFA`
--

CREATE TABLE `TAREFA` (
  `IDT` int(11) NOT NULL,
  `TITULO` varchar(150) NOT NULL,
  `DESCRICAO` text DEFAULT NULL,
  `ESTADO` enum('Por fazer','Em curso','Terminada') NOT NULL DEFAULT 'Por fazer',
  `DATAFINAL` datetime DEFAULT NULL,
  `IDP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `UTILIZADOR`
--

CREATE TABLE `UTILIZADOR` (
  `IDU` int(11) NOT NULL,
  `NOME` varchar(100) NOT NULL,
  `EMAIL` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `TIPO` enum('Aluno','Professor') NOT NULL DEFAULT 'Aluno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `UTILIZADOR`
--

INSERT INTO `UTILIZADOR` (`IDU`, `NOME`, `EMAIL`, `PASSWORD`, `TIPO`) VALUES
(1, 'Mariana', 'aluno@etpc.pt', '$2y$10$CSpDr1Fg9S2PYH/1zVYLOO5kzExLu2t39WatV1xUflxxx3r/gTMuG', 'Aluno'),
(2, 'Professor', 'professor@etpc.pt', '$2y$10$vBR8XiTxB3A.YHpzp8HhxOWKqYKpXrd5RmwozHbQlVezcc2537Vr6', 'Professor');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `FEEDBACK`
--
ALTER TABLE `FEEDBACK`
  ADD PRIMARY KEY (`IDF`),
  ADD KEY `fk_feedback_projeto` (`IDP`),
  ADD KEY `fk_feedback_professor` (`IDProfessor`);

--
-- Índices para tabela `PROJETO`
--
ALTER TABLE `PROJETO`
  ADD PRIMARY KEY (`IDP`),
  ADD KEY `fk_projeto_aluno` (`IDAluno`),
  ADD KEY `fk_projeto_professor` (`IDProfessor`);

--
-- Índices para tabela `RECURSO`
--
ALTER TABLE `RECURSO`
  ADD PRIMARY KEY (`IDR`),
  ADD KEY `fk_recurso_projeto` (`IDP`);

--
-- Índices para tabela `REGISTO`
--
ALTER TABLE `REGISTO`
  ADD PRIMARY KEY (`IDR`),
  ADD KEY `fk_registo_projeto` (`IDP`);

--
-- Índices para tabela `TAREFA`
--
ALTER TABLE `TAREFA`
  ADD PRIMARY KEY (`IDT`),
  ADD KEY `fk_tarefa_projeto` (`IDP`);

--
-- Índices para tabela `UTILIZADOR`
--
ALTER TABLE `UTILIZADOR`
  ADD PRIMARY KEY (`IDU`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `FEEDBACK`
--
ALTER TABLE `FEEDBACK`
  MODIFY `IDF` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `PROJETO`
--
ALTER TABLE `PROJETO`
  MODIFY `IDP` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `RECURSO`
--
ALTER TABLE `RECURSO`
  MODIFY `IDR` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `REGISTO`
--
ALTER TABLE `REGISTO`
  MODIFY `IDR` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `TAREFA`
--
ALTER TABLE `TAREFA`
  MODIFY `IDT` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `UTILIZADOR`
--
ALTER TABLE `UTILIZADOR`
  MODIFY `IDU` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `FEEDBACK`
--
ALTER TABLE `FEEDBACK`
  ADD CONSTRAINT `fk_feedback_professor` FOREIGN KEY (`IDProfessor`) REFERENCES `UTILIZADOR` (`IDU`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_feedback_projeto` FOREIGN KEY (`IDP`) REFERENCES `PROJETO` (`IDP`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `PROJETO`
--
ALTER TABLE `PROJETO`
  ADD CONSTRAINT `fk_projeto_aluno` FOREIGN KEY (`IDAluno`) REFERENCES `UTILIZADOR` (`IDU`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_projeto_professor` FOREIGN KEY (`IDProfessor`) REFERENCES `UTILIZADOR` (`IDU`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `RECURSO`
--
ALTER TABLE `RECURSO`
  ADD CONSTRAINT `fk_recurso_projeto` FOREIGN KEY (`IDP`) REFERENCES `PROJETO` (`IDP`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `REGISTO`
--
ALTER TABLE `REGISTO`
  ADD CONSTRAINT `fk_registo_projeto` FOREIGN KEY (`IDP`) REFERENCES `PROJETO` (`IDP`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `TAREFA`
--
ALTER TABLE `TAREFA`
  ADD CONSTRAINT `fk_tarefa_projeto` FOREIGN KEY (`IDP`) REFERENCES `PROJETO` (`IDP`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
