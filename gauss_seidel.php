<?php
function gaussSeidel($A, $b, $x0, $tol=1e-6, $maxIter=100) {
    $n = count($b);
    $x = $x0;
    $history = [];

    for ($k=0; $k<$maxIter; $k++) {
        $x_new = $x;
        for ($i=0; $i<$n; $i++) {
            $s1 = 0;
            for ($j=0; $j<$i; $j++) $s1 += $A[$i][$j] * $x_new[$j];
            $s2 = 0;
            for ($j=$i+1; $j<$n; $j++) $s2 += $A[$i][$j] * $x[$j];
            $x_new[$i] = ($b[$i] - $s1 - $s2) / $A[$i][$i];
        }
        $error = 0;
        for ($i=0; $i<$n; $i++) $error = max($error, abs($x_new[$i] - $x[$i]));
        $history[] = $error;

        if ($error < $tol) return [$x_new, $k+1, $history];
        $x = $x_new;
    }
    return [$x, $maxIter, $history];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $n = intval($_POST["n"]);
    $A = [];
    for ($i=0; $i<$n; $i++) {
        $row = array_map("floatval", explode(" ", $_POST["A".$i]));
        $A[] = $row;
    }
    $b = array_map("floatval", explode(" ", $_POST["b"]));
    $x0 = array_fill(0, $n, 0.0);
    $tol = floatval($_POST["tol"]);
    $maxIter = intval($_POST["maxIter"]);

    [$sol, $it, $history] = gaussSeidel($A, $b, $x0, $tol, $maxIter);
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Gauss-Seidel</title></head>
<body>
<h1>Método de Gauss-Seidel</h1>
<form method="post">
    Número de variáveis: <input type="number" name="n" value="3" min="2" max="10"><br>
    <p>Digite cada linha da matriz A (valores separados por espaço):</p>
    <input type="text" name="A0" value="4 -1 0"><br>
    <input type="text" name="A1" value="-1 4 -1"><br>
    <input type="text" name="A2" value="0 -1 3"><br>
    Vetor b: <input type="text" name="b" value="15 10 10"><br>
    Tolerância: <input type="text" name="tol" value="1e-6"><br>
    Máx Iterações: <input type="number" name="maxIter" value="100"><br>
    <button type="submit">Calcular</button>
</form>

<?php if (isset($sol)): ?>
    <h2>Resultado</h2>
    <p>Solução aproximada: <?php echo implode(", ", $sol); ?></p>
    <p>Iterações: <?php echo $it; ?></p>
    <p>Erros: <?php echo implode(", ", $history); ?></p>
<?php endif; ?>
</body>
</html>
