const borderColorCycle = document.getElementById('js-border-color-cycle');

const cycles = [
    'border-blue-200',
    'border-cyan-200',
    'border-teal-200',
    'border-green-200',
    'border-red-200',
];

let currentColor = 0;

cycle();

function cycle() {
    setTimeout(() => {
        borderColorCycle.classList.remove(cycles[currentColor]);

        currentColor++;

        if (currentColor >= cycles.length) {
            currentColor = 0;
        }

        borderColorCycle.classList.add(cycles[currentColor]);

        cycle();
    }, 5000)
}
