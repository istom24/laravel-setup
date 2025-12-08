<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>TaskFlow+ Live</title>
    @vite('resources/js/app.js')
</head>
<body>
<h2>Події у реальному часі</h2>
<div id="log"></div>

<script>
    const projectId = 7;

    const log = msg => {
        const el = document.getElementById('log');
        el.innerHTML += `<p>${msg}</p>`;
    };

    document.addEventListener('DOMContentLoaded', function () {

        console.log('Echo? => ', window.Echo);

        if (!window.Echo) {
            log("Echo не завантажився");
            return;
        }

        window.Echo.private(`project.${projectId}`)
            .listen('.task.updated', (e) => {
                log(`🟡 Задача "${e.title}" змінена (${e.status})`);
            })
            .listen('.comment.created', (e) => {
                log(`💬 Новий коментар до задачі #${e.task_id}: ${e.body} (автор: ${e.author})`);
            });
    });
</script>


</body>
</html>
