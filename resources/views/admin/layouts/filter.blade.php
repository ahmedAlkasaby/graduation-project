<script>
    function toggleSearchForm() {
        var form = document.getElementById('searchForm');
        var arrow = document.getElementById('toggleArrow');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            arrow.classList.remove('ti-arrow-down');
            arrow.classList.add('ti-arrow-up');
        } else {
            form.style.display = 'none';
            arrow.classList.remove('ti-arrow-up');
            arrow.classList.add('ti-arrow-down');
        }
    }

    function toggleDeleteForm() {
        var form = document.getElementById('deleteForm');
        var arrow = document.getElementById('toggleDeleteArrow');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            arrow.classList.remove('ti-arrow-down');
            arrow.classList.add('ti-arrow-up');
        } else {
            form.style.display = 'none';
            arrow.classList.remove('ti-arrow-up');
            arrow.classList.add('ti-arrow-down');
        }
    }
</script>
