<?php
// ── Conexão com o banco de dados ──────────────────────────────────────────────
$host     = 'localhost';
$dbname   = 'deliverydesushi';
$user     = 'root';       // altere conforme seu ambiente
$password = '';           // altere conforme seu ambiente

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}

// ── Busca todas as categorias que possuem produtos disponíveis ────────────────
$sqlCategorias = "
    SELECT c.id_categoria, c.nome_categoria
    FROM categorias c
    INNER JOIN produtos p ON p.id_categoria = c.id_categoria
    WHERE p.disponivel = 1
    GROUP BY c.id_categoria, c.nome_categoria
    ORDER BY c.id_categoria
";
$categorias = $pdo->query($sqlCategorias)->fetchAll(PDO::FETCH_ASSOC);

// ── Busca todos os produtos disponíveis ───────────────────────────────────────
$sqlProdutos = "
    SELECT id_produto, id_categoria, nome, descricao, preco, imagem
    FROM produtos
    WHERE disponivel = 1
    ORDER BY id_categoria, id_produto
";
$todosProdutos = $pdo->query($sqlProdutos)->fetchAll(PDO::FETCH_ASSOC);

// ── Agrupa produtos por categoria ─────────────────────────────────────────────
$produtosPorCategoria = [];
foreach ($todosProdutos as $produto) {
    $produtosPorCategoria[$produto['id_categoria']][] = $produto;
}

// ── Função auxiliar: renderiza imagem ou placeholder ─────────────────────────
function imagemProduto(string $imagem = null): string {
    if ($imagem && file_exists($imagem)) {
        return htmlspecialchars($imagem);
    }
    return '_img/placeholder.jpg'; // imagem padrão caso não haja cadastrada
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="_css/style.css">
    <link rel="stylesheet" href="_css/cardapio.css">
    <link rel="shortcut icon" href="_img/Logo.png" type="image/x-icon">
    <title>Cardápio</title>
</head>
<body>

    <header class="menu">
        <div class="logo">
            <a href="index.html"><img src="_img/Logo.png" alt="Logo Sushi House"></a>
        </div>
        <div class="links">
            <ul class="nav">
                <li><a href="index.php" target="_blank">Home</a></li>
                <li><a href="cardapio.php" target="_blank">Cardápio</a></li>
                <li><a href="#suporte">Contato</a></li>
            </ul>
        </div>
        <div class="login">
            <button><img src="_img/Login.png" alt="Login"></button>
        </div>
    </header>

    <section class="produtos">
        <div class="casa-header">
            <p>CARDÁPIO</p>
            <h2>Peça <span>Seus Favoritos</span></h2>
            <p id="txt">Um espaço pensado para unir tradição japonesa, ingredientes frescos e um atendimento que faz você se sentir em casa.</p>
        </div>

        <?php if (empty($categorias)): ?>
            <p style="text-align:center; padding: 2rem;">Nenhum produto disponível no momento.</p>

        <?php else: ?>
            <?php foreach ($categorias as $categoria): ?>
                <?php
                    $idCat   = $categoria['id_categoria'];
                    $nomeCat = htmlspecialchars($categoria['nome_categoria']);
                    $produtos = $produtosPorCategoria[$idCat] ?? [];
                ?>

                <div class="combos">
                    <h1><?= $nomeCat ?></h1>
                    <div class="combos-container">

                        <?php foreach ($produtos as $index => $produto): ?>
                            <?php
                                $classe = 'i' . (($index % 4) + 1); // i1, i2, i3, i4 (cicla de 4 em 4)
                                $nome   = htmlspecialchars($produto['nome']);
                                $desc   = htmlspecialchars($produto['descricao'] ?? '');
                                $preco  = number_format((float)$produto['preco'], 2, ',', '.');
                                $img    = imagemProduto($produto['imagem']);
                                $id     = (int)$produto['id_produto'];
                            ?>
                            <div class="<?= $classe ?>">
                                <div class="img">
                                    <img src="<?= $img ?>" alt="<?= $nome ?>">
                                </div>

                                <div class="txt">
                                    <h1><?= $nome ?></h1>
                                    <p><?= $desc ?></p>
                                    <h2>R$ <?= $preco ?></h2>
                                </div>

                                <button onclick="adicionarCarrinho(<?= $id ?>)">
                                    Adicionar ao carrinho
                                </button>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>

    </section>

    <footer>
        <p>© 2026 Sushi <span>House</span> — Todos os direitos reservados.</p>
        <p>Site por: Claudio Henrique, Gabriel Lopes e Eduardo Gomes.</p>
    </footer>

    <script>
        function adicionarCarrinho(idProduto) {
            // Implemente aqui a lógica do carrinho (AJAX, redirect, etc.)
            console.log('Produto adicionado:', idProduto);
            alert('Produto adicionado ao carrinho! (ID: ' + idProduto + ')');
        }
    </script>

</body>
</html>