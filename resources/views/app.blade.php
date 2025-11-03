<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>App</title>
  @vite(['resources/js/spa/main.ts'])
  <script>
    (() => {
      try {
        const saved = localStorage.getItem('darkMode');
        const isDark = saved ? JSON.parse(saved) : false;
        if (isDark) document.documentElement.classList.add('dark');
      } catch {}
    })();
  </script>
</head>
<body>
  <div id="app"></div>
</body>
</html>

