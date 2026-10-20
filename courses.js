const courseSearch = document.getElementById('courseSearch');
const courseRows = Array.from(document.querySelectorAll('.course-row'));
const courseCount = document.getElementById('courseCount');
const noSearchResults = document.getElementById('noSearchResults');

if (courseSearch && courseCount) {
    const totalCourses = courseRows.length;
    courseSearch.addEventListener('input', () => {
        const query = courseSearch.value.trim().toLocaleLowerCase('id');
        let visibleCourses = 0;

        courseRows.forEach((row) => {
            const matches = row.dataset.courseName.toLocaleLowerCase('id').includes(query);
            row.hidden = !matches;
            if (matches) visibleCourses++;
        });

        if (noSearchResults) noSearchResults.hidden = visibleCourses > 0;
        courseCount.textContent = query
            ? `${visibleCourses} dari ${totalCourses} kelas cocok`
            : `${totalCourses} kelas`;
    });
}
