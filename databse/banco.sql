--
-- Estrutura da tabela `boleto`
--

CREATE TABLE `boleto` (
  `id` int(11) NOT NULL,
  `nota_fiscal` varchar(50) NOT NULL,
  `valor` varchar(30) NOT NULL,
  `vencimento` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `evento`
--

CREATE TABLE `evento` (
  `id` int(11) NOT NULL,
  `id_nota` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `horario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedor`
--

CREATE TABLE `fornecedor` (
  `cnpj` varchar(20) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nota_fiscal`
--

CREATE TABLE `nota_fiscal` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `numero` varchar(30) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `cnpj_emit` varchar(30) NOT NULL,
  `mes` varchar(10) NOT NULL,
  `ano` varchar(10) NOT NULL,
  `fornecedor` varchar(60) NOT NULL,
  `valor` varchar(30) NOT NULL,
  `data_recebimento` varchar(30) NOT NULL,
  `horario_chegada` varchar(30) NOT NULL,
  `status` varchar(30) NOT NULL,
  `status_oper` varchar(50) DEFAULT NULL,
  `erro` varchar(3) NOT NULL,
  `comprador` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `boleto`
--
ALTER TABLE `boleto`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_nota` (`id_nota`);

--
-- Índices para tabela `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD PRIMARY KEY (`cnpj`);

--
-- Índices para tabela `nota_fiscal`
--
ALTER TABLE `nota_fiscal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `boleto`
--
ALTER TABLE `boleto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `evento`
--
ALTER TABLE `evento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nota_fiscal`
--
ALTER TABLE `nota_fiscal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `evento_nota` FOREIGN KEY (`id_nota`) REFERENCES `nota_fiscal` (`id`);
COMMIT;
