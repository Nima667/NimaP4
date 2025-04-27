document.addEventListener('DOMContentLoaded', () => {
    let timers = {};
    let startTimes = {};

    function getIranTime(date = new Date()) {
        const iranOffsetMs = (3.5 * 60 * 60 * 1000);
        const iranDate = new Date(date.getTime() + iranOffsetMs);
        return iranDate.toISOString().slice(0, 19).replace('T', ' ');
    }

    // تابع برای شروع تایمر
    function startTimer(systemId, startTime, totalTimeInSeconds, costPerSecond) {
        const timerElement = document.getElementById(`timer-${systemId}`);
        const timeDisplay = document.getElementById(`time-${systemId}`);
        const startButton = document.querySelector(`.start-btn[data-id="${systemId}"]`);
        const endButton = document.querySelector(`.end-btn[data-id="${systemId}"]`);

        timerElement.style.display = 'block';
        startButton.style.display = 'none';
        endButton.style.display = 'inline-block';

        let elapsedTime = Math.floor((new Date() - new Date(startTime)) / 1000);
        let remainingTime = totalTimeInSeconds - elapsedTime;

        if (remainingTime <= 0) {
            clearInterval(timers[systemId]);
            alert('زمان استفاده به پایان رسید.');
            timerElement.style.display = 'none';
            endButton.click();
            return;
        }

        timers[systemId] = setInterval(() => {
            remainingTime--;
            const hours = Math.floor(remainingTime / 3600);
            const minutes = Math.floor((remainingTime % 3600) / 60);
            const seconds = remainingTime % 60;

            timeDisplay.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (remainingTime <= 0) {
                clearInterval(timers[systemId]);
                alert('زمان استفاده به پایان رسید.');
                timerElement.style.display = 'none';
                endButton.click();
            }
        }, 1000);
    }

    // چک کردن تایمرهای ذخیره شده در localStorage
    document.querySelectorAll('.start-btn').forEach(btn => {
        const systemId = btn.dataset.id;
        const savedData = localStorage.getItem(`timer_${systemId}`);
        if (savedData) {
            const { startTime, totalTimeInSeconds, costPerSecond } = JSON.parse(savedData);
            startTimes[systemId] = new Date(startTime);
            startTimer(systemId, startTime, totalTimeInSeconds, costPerSecond);
        }
    });

    // دکمه شروع
    document.querySelectorAll('.start-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const systemId = btn.dataset.id;
            const costPerSecond = parseFloat(btn.dataset.costPerSecond);
            if (isNaN(costPerSecond)) {
                alert('خطا: هزینه به ازای هر ثانیه نامعتبر است.');
                return;
            }

            const durationInMinutes = prompt('مدت زمان استفاده را به دقیقه وارد کنید:');
            if (durationInMinutes && !isNaN(durationInMinutes)) {
                const totalTimeInSeconds = parseInt(durationInMinutes) * 60;
                if (totalTimeInSeconds > 0) {
                    const startTime = new Date();
                    startTimes[systemId] = startTime;

                    localStorage.setItem(`start_time_${systemId}`, getIranTime(startTime));
                    localStorage.setItem(`timer_${systemId}`, JSON.stringify({
                        startTime: startTime.toISOString(),
                        totalTimeInSeconds,
                        costPerSecond
                    }));

                    startTimer(systemId, startTime, totalTimeInSeconds, costPerSecond);
                } else {
                    alert('مدت زمان باید بیشتر از 0 باشد.');
                }
            }
        });
    });

    // دکمه پایان
    document.querySelectorAll('.end-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const systemId = btn.dataset.id;

            clearInterval(timers[systemId]);
            localStorage.removeItem(`timer_${systemId}`);

            const timerElement = document.getElementById(`timer-${systemId}`);
            timerElement.style.display = 'none';

            const endTime = new Date();
            const startTime = startTimes[systemId];
            if (!startTime) {
                alert('خطا: زمان شروع یافت نشد.');
                return;
            }

            const elapsedTimeInSeconds = Math.floor((endTime - startTime) / 1000);
            const costPerSecond = parseFloat(document.querySelector(`.start-btn[data-id="${systemId}"]`).dataset.costPerSecond);
            if (isNaN(costPerSecond)) {
                alert('خطا: هزینه به ازای هر ثانیه نامعتبر است.');
                return;
            }

            const totalCost = (elapsedTimeInSeconds * costPerSecond).toFixed(2);
            const start_time = localStorage.getItem(`start_time_${systemId}`);
            const end_time = getIranTime(endTime);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(`مبلغ دریافتی: ${totalCost} تومان`);
                }
            };
            xhr.send(`save_record=true&system_id=${systemId}&start_time=${start_time}&end_time=${end_time}&total_cost=${totalCost}`);

            btn.style.display = 'none';
            document.querySelector(`.start-btn[data-id="${systemId}"]`).style.display = 'inline-block';
        });
    });

    // مدیریت ویرایش سیستم‌ها
    document.querySelectorAll('.edit-system-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit-id').value = btn.dataset.id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('last_service').value = btn.dataset.lastService;
            document.getElementById('cost_per_second').value = btn.dataset.costPerSecond;
            document.getElementById('edit-btn').style.display = 'inline-block';
        });
    });
});
