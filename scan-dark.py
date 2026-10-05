import re, pathlib

need = {
    'bg-white': 'dark:bg-', 'bg-slate-50': 'dark:bg-', 'bg-slate-100': 'dark:bg-',
    'bg-gray-50': 'dark:bg-', 'bg-gray-100': 'dark:bg-', 'bg-orange-50': 'dark:bg-',
    'bg-emerald-50': 'dark:bg-', 'text-slate-900': 'dark:text-', 'text-slate-800': 'dark:text-',
    'text-slate-700': 'dark:text-', 'text-slate-600': 'dark:text-', 'text-slate-500': 'dark:text-',
    'text-gray-900': 'dark:text-', 'text-gray-700': 'dark:text-', 'text-gray-500': 'dark:text-',
    'text-emerald-700': 'dark:text-', 'text-emerald-600': 'dark:text-', 'text-orange-700': 'dark:text-',
    'text-orange-600': 'dark:text-', 'border-slate-200': 'dark:border-', 'border-slate-100': 'dark:border-',
    'border-gray-200': 'dark:border-', 'divide-slate-200': 'dark:divide-',
}
tot = 0
for f in sorted(pathlib.Path('resources/views').rglob('*.blade.php')):
    src = f.read_text(encoding='utf-8')
    for m in re.finditer(r'class="([^"]*)"', src):
        cls = ' '.join(m.group(1).split())
        for light, darkpref in need.items():
            if re.search(r'(?<![\w:-])' + re.escape(light) + r'(?![\w-])', cls) and darkpref not in cls:
                line = src[:m.start()].count('\n') + 1
                print(f'{f}:{line} :: {light} tanpa {darkpref} :: {cls[:130]}')
                tot += 1
                break
print('TOTAL temuan:', tot)
