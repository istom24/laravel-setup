<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>TaskFlow+ Live</title>
    @vite(['resources/js/app.js'])
</head>
<body>
<h2>Події у реальному часі</h2>
<div id="log"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const projectId = 1;
        const log = msg => {
            const el = document.getElementById('log');
            if(el) el.innerHTML += `<p>${msg}</p>`;
        };

        if (window.Echo) {
            console.log('Echo ініціалізовано, підключаємось...');

            window.Echo.private(`project.${projectId}`)
                .listen('.task.updated', (e) => {
                    console.log('Отримано подію TaskUpdated:', e);
                    log(`🟡 Задача "${e.title}" змінена (${e.status})`);
                })
                .listen('.comment.created', (e) => {
                    console.log('Отримано подію CommentCreated:', e);
                    log(`💬 Новий коментар до задачі #${e.task_id}: ${e.body}`);
                });
        } else {
            console.error('Laravel Echo не завантажено');
        }
    });
</script>
</body>
</html>
