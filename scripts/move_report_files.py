"""Move protected evidence out of public, preserving database-relative paths.
Run from the repository root after deploying PrivateReportFiles-aware controllers.
A private tar backup and checksum verification precede removal of each public file.
"""
from pathlib import Path
import hashlib
import shutil
import tarfile
from datetime import datetime

root = Path(__file__).resolve().parents[1]
folders = [root / 'public/uploads' / name for name in ('lots', 'supplier_doc', 'company_doc')]
files = [p for folder in folders if folder.exists() for p in folder.rglob('*') if p.is_file()]
if any(p.is_symlink() for folder in folders if folder.exists() for p in folder.rglob('*')):
    raise RuntimeError('Symlinks require manual review')
def digest(path):
    with path.open('rb') as stream:
        checksum = hashlib.sha256()
        for chunk in iter(lambda: stream.read(1024 * 1024), b''):
            checksum.update(chunk)
        return checksum.hexdigest()
for source in files:
    target = root / 'storage/app/private' / source.relative_to(root / 'public')
    if target.exists() and digest(target) != digest(source):
        raise RuntimeError(f'Conflicting destination: {target}')
backup_dir = root / 'storage/app/backups'
backup_dir.mkdir(parents=True, exist_ok=True)
backup = backup_dir / ('report_files_' + datetime.now().strftime('%Y%m%d_%H%M%S') + '.tar.gz')
with tarfile.open(backup, 'w:gz') as archive:
    for source in files:
        archive.add(source, arcname=str(source.relative_to(root / 'public')))
backup.chmod(0o600)
for source in files:
    target = root / 'storage/app/private' / source.relative_to(root / 'public')
    target.parent.mkdir(parents=True, exist_ok=True)
    shutil.copy2(source, target)
    if digest(source) != digest(target):
        raise RuntimeError(f'Checksum mismatch: {source}')
    source.unlink()
print(f'Moved and verified {len(files)} files; backup: {backup}')
