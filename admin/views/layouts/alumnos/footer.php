        </main>
    </div>

    <script>
        const todayDateEl = document.getElementById('today-date');
        if(todayDateEl) {
            const d = new Date();
            const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
            todayDateEl.textContent = d.toLocaleDateString('es-MX', opts);
        }
    </script>
</body>
</html>
