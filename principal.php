<?php  
require 'conexao.php'; // Make sure this file is included at the top

$consultaResultado = null;
$mensagem = '';

// Verifica a ação solicitada no formulário
if (isset($_POST['acao'])) {
    $numero = $_POST['numero'];
    $descricao = $_POST['descricao'] ?? ''; // Usar valor vazio se não estiver definido
    $valor = $_POST['valor'] ?? 0; // Usar 0 se não estiver definido

    switch ($_POST['acao']) {
        case 'incluir':
            $sql = "INSERT INTO Plano (Numero, Descricao, Valor) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql); // Use prepared statements to prevent SQL injection
            if ($stmt->execute([$numero, $descricao, $valor])) {
                $mensagem = "Plano incluído com sucesso!";
            } else {
                $mensagem = "Erro ao incluir o plano.";
            }
            break;

        case 'alterar':
            $sql = "UPDATE Plano SET Descricao=?, Valor=? WHERE Numero=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$descricao, $valor, $numero])) {
                $mensagem = "Plano alterado com sucesso!";
            } else {
                $mensagem = "Erro ao alterar o plano.";
            }
            break;

        case 'excluir':
            $sql = "DELETE FROM Plano WHERE Numero=?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$numero])) {
                $mensagem = "Plano excluído com sucesso!";
            } else {
                $mensagem = "Erro ao excluir o plano.";
            }
            break;

        case 'consultar':
            $sql = "SELECT * FROM Plano WHERE Numero=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$numero]);
            $consultaResultado = $stmt->fetchAll(); // Fetch all results
            if (!$consultaResultado) {
                $mensagem = "Nenhum plano encontrado com o número informado.";
            }
            break;
    }
}

// Consulta todos os planos para exibir em uma tabela
$sql = "SELECT * FROM Plano";
$result = $pdo->query($sql)->fetchAll(); // Fetch all results
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Manutenção de Plano</title>
</head>
<body>
    <h2>Cadastro de Plano</h2>
    <form action="principal.php" method="post">
        <label for="numero">Número:</label>
        <input type="text" name="numero" required><br>

        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao"><br>

        <label for="valor">Valor:</label>
        <input type="number" name="valor" step="0.01"><br>

        <button type="submit" name="acao" value="incluir">Incluir</button>
        <button type="submit" name="acao" value="alterar">Alterar</button>
        <button type="submit" name="acao" value="excluir">Excluir</button>
        <button type="submit" name="acao" value="consultar">Consultar</button>
    </form>

    <p><?php echo $mensagem; ?></p> <!-- Exibir mensagens -->

    <?php if ($consultaResultado && count($consultaResultado) > 0): ?>
        <h2>Resultado da Consulta</h2>
        <table border="1">
            <tr>
                <th>Número</th>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>
            <?php foreach ($consultaResultado as $row) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['Numero']) ?></td>
                    <td><?= htmlspecialchars($row['Descricao']) ?></td>
                    <td><?= htmlspecialchars($row['Valor']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
