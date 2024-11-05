<?php

require 'conexão.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Clientes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Cadastro de Clientes</h1>
        <div class="navigation">
            <a href="index.php">← Voltar para a Página Principal</a>
        </div>
    </header>
    
    <div class="container">
        
        
        <h2>Incluir Cliente</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incluir'])) {
            $plano = $_POST['plano'];
            $endereco = $_POST['endereco'];
            $cidade = $_POST['cidade'];
            $estado = strtoupper($_POST['estado']); // Garantir que o estado seja em maiúsculas
            $cep = $_POST['cep'];
            $nome = $_POST['nome'];

            try {
                $sql = "INSERT INTO cliente (nome, plano, endereco, cidade, estado, cep) 
                        VALUES (:nome, :plano, :endereco, :cidade, :estado, :cep)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nome' => $nome,
                    ':plano' => $plano,
                    ':endereco' => $endereco,
                    ':cidade' => $cidade,
                    ':estado' => $estado,
                    ':cep' => $cep
                ]);
                echo '<div class="alert">Cliente incluído com sucesso!</div>';
            } catch (PDOException $e) {
                echo '<div class="error">Erro ao incluir o cliente: ' . $e->getMessage() . '</div>';
            }
        }
        ?>
        <form action="cadastro_associado.php" method="post">
            <input type="hidden" name="incluir" value="1">
            
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" maxlength="40" required>

            <label for="plano">Plano:</label>
            <select name="plano" id="plano" required>
                <option value="">Selecione</option>
                <?php
                
                try {
                    $sql = "SELECT numero, descricao, valor FROM plano";
                    $stmt = $pdo->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($row['numero']) . '">' . htmlspecialchars($row['descricao']) . ' - R$ ' . htmlspecialchars($row['valor']) . '</option>';
                    }
                } catch (PDOException $e) {
                    echo '<option value="">Erro ao carregar planos</option>';
                }
                ?>
            </select>

            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" id="endereco" maxlength="35" required>

            <label for="cidade">Cidade:</label>
            <input type="text" name="cidade" id="cidade" maxlength="20" required>

            <label for="estado">Estado:</label>
            <input type="text" name="estado" id="estado" maxlength="2" required>

            <label for="cep">CEP:</label>
            <input type="text" name="cep" id="cep" maxlength="9" placeholder="Ex: 12345-678" required>

            <button type="submit">Incluir Cliente</button>
        </form>

        
        
        <h2>Alterar Cliente</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['alterar'])) {
            $nome_original = $_POST['nome_original'];
            $nome = $_POST['nome'];
            $plano = $_POST['plano'];
            $endereco = $_POST['endereco'];
            $cidade = $_POST['cidade'];
            $estado = strtoupper($_POST['estado']);
            $cep = $_POST['cep'];

            try {
                $sql = "UPDATE cliente 
                        SET nome = :nome, plano = :plano, endereco = :endereco, 
                            cidade = :cidade, estado = :estado, cep = :cep 
                        WHERE nome = :nome_original";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nome' => $nome,
                    ':plano' => $plano,
                    ':endereco' => $endereco,
                    ':cidade' => $cidade,
                    ':estado' => $estado,
                    ':cep' => $cep,
                    ':nome_original' => $nome_original
                ]);
                echo '<div class="alert">Cliente alterado com sucesso!</div>';
            } catch (PDOException $e) {
                echo '<div class="error">Erro ao alterar o cliente: ' . $e->getMessage() . '</div>';
            }
        }


        $nome_alterar = '';
        $plano_alterar = '';
        $endereco_alterar = '';
        $cidade_alterar = '';
        $estado_alterar = '';
        $cep_alterar = '';
        $nome_original = '';

        if (isset($_GET['alterar_nome'])) {
            $nome_original = $_GET['alterar_nome'];
            try {
                $sql = "SELECT * FROM cliente WHERE nome = :nome";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':nome' => $nome_original]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($cliente) {
                    $nome_alterar = $cliente['nome'];
                    $plano_alterar = $cliente['plano'];
                    $endereco_alterar = $cliente['endereco'];
                    $cidade_alterar = $cliente['cidade'];
                    $estado_alterar = $cliente['estado'];
                    $cep_alterar = $cliente['cep'];
                } else {
                    echo '<div class="error">Cliente não encontrado.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error">Erro ao buscar o cliente: ' . $e->getMessage() . '</div>';
            }
        }
        ?>
        <form action="cliente.php" method="post">
            <input type="hidden" name="alterar" value="1">
            
            <?php if ($nome_original): ?>
                <input type="hidden" name="nome_original" value="<?php echo htmlspecialchars($nome_original); ?>">
            <?php endif; ?>

            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" maxlength="40" value="<?php echo htmlspecialchars($nome_alterar); ?>" required>

            <label for="plano">Plano:</label>
            <select name="plano" id="plano" required>
                <option value="">Selecione</option>
                <?php
            
                try {
                    $sql = "SELECT numero, descricao, valor FROM plano";
                    $stmt = $pdo->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = ($plano_alterar == $row['numero']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($row['numero']) . '" ' . $selected . '>' . htmlspecialchars($row['descricao']) . ' - R$ ' . htmlspecialchars($row['valor']) . '</option>';
                    }
                } catch (PDOException $e) {
                    echo '<option value="">Erro ao carregar planos</option>';
                }
                ?>
            </select>

            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" id="endereco" maxlength="35" value="<?php echo htmlspecialchars($endereco_alterar); ?>" required>

            <label for="cidade">Cidade:</label>
            <input type="text" name="cidade" id="cidade" maxlength="20" value="<?php echo htmlspecialchars($cidade_alterar); ?>" required>

            <label for="estado">Estado:</label>
            <input type="text" name="estado" id="estado" maxlength="2" value="<?php echo htmlspecialchars($estado_alterar); ?>" required>

            <label for="cep">CEP:</label>
            <input type="text" name="cep" id="cep" maxlength="9" placeholder="Ex: 12345-678" value="<?php echo htmlspecialchars($cep_alterar); ?>" required>

            <button type="submit">Alterar Cliente</button>
        </form>

        
        <!-- Excluir Cliente -->
        <h2>Excluir Cliente</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
            $nome = $_POST['nome_excluir'];

            try {
                $sql = "DELETE FROM cliente WHERE nome = :nome";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':nome' => $nome]);

                if ($stmt->rowCount()) {
                    echo '<div class="alert">Cliente excluído com sucesso!</div>';
                } else {
                    echo '<div class="error">Cliente não encontrado.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error">Erro ao excluir o cliente: ' . $e->getMessage() . '</div>';
            }
        }
        ?>
        <form action="cliente.php" method="post">
            <input type="hidden" name="excluir" value="1">
            
            <label for="nome_excluir">Nome do Cliente:</label>
            <input type="text" name="nome_excluir" id="nome_excluir" maxlength="40" required>

            <button type="submit">Excluir Cliente</button>
        </form>

        
        <!-- Buscar Cliente -->
        <h2>Buscar Cliente</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['buscar'])) {
            $nome = $_GET['buscar'];

            try {
                $sql = "SELECT c.nome, c.endereco, c.cidade, c.estado, c.cep, p.descricao, p.valor 
                        FROM cliente c 
                        JOIN plano p ON c.plano = p.numero 
                        WHERE c.nome = :nome";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':nome' => $nome]);
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($cliente) {
                    echo '<h3>Detalhes do Cliente</h3>';
                    echo '<p><strong>Nome:</strong> ' . htmlspecialchars($cliente['nome']) . '</p>';
                    echo '<p><strong>Plano:</strong> ' . htmlspecialchars($cliente['descricao']) . ' - R$ ' . htmlspecialchars($cliente['valor']) . '</p>';
                    echo '<p><strong>Endereço:</strong> ' . htmlspecialchars($cliente['endereco']) . '</p>';
                    echo '<p><strong>Cidade:</strong> ' . htmlspecialchars($cliente['cidade']) . '</p>';
                    echo '<p><strong>Estado:</strong> ' . htmlspecialchars($cliente['estado']) . '</p>';
                    echo '<p><strong>CEP:</strong> ' . htmlspecialchars($cliente['cep']) . '</p>';
                } else {
                    echo '<div class="error">Cliente não encontrado.</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error">Erro ao buscar o cliente: ' . $e->getMessage() . '</div>';
            }
        }
        ?>
        <form action="cliente.php" method="get">
            <label for="buscar_nome">Nome do Cliente:</label>
            <input type="text" name="buscar" id="buscar_nome" maxlength="40" required>

            <button type="submit">Buscar Cliente</button>
        </form>

        
        <!-- Listagem de Clientes -->
        <h2>Listagem de Clientes</h2>
        <?php
        try {
            $sql = "SELECT c.nome, c.endereco, c.cidade, c.estado, c.cep, p.descricao, p.valor 
                    FROM cliente c 
                    JOIN plano p ON c.plano = p.numero";
            $stmt = $pdo->query($sql);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($clientes) {
                echo '<div class="table-container">';
                echo '<table>';
                echo '<tr>
                        <th>Nome</th>
                        <th>Plano</th>
                        <th>Valor do Plano (R$)</th>
                        <th>Endereço</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                        <th>CEP</th>
                        <th>Ações</th>
                      </tr>';
                foreach ($clientes as $cliente) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($cliente['nome']) . '</td>';
                    echo '<td>' . htmlspecialchars($cliente['descricao']) . '</td>';
                    echo '<td>' . htmlspecialchars($cliente['valor']) . '</td>';
                    echo '<td>' . htmlspecialchars($cliente['endereco']) . '</td>';
                    echo '<td>' . htmlspecialchars($cliente['cidade']) . '</td>';
                    echo '<td>' . htmlspecialchars($cliente['estado']) . '</td>';
                    echo '<td>' . htmlspecialchars($cliente['cep']) . '</td>';
                    echo '<td>';
                    echo '<a href="cliente.php?alterar_nome=' . urlencode($cliente['nome']) . '">Alterar</a> | ';
                    echo '<a href="cliente.php?buscar=' . urlencode($cliente['nome']) . '">Buscar</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';
            } else {
                echo '<p>Nenhum cliente encontrado.</p>';
            }
        } catch (PDOException $e) {
            echo '<div class="error">Erro ao listar os clientes: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Longa Vida. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
