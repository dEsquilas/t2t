# Project Preferences

## Development Environment
- **Always use `sail` command** instead of direct `php` commands
- **Package manager**: Use `pnpm` instead of `npm`
- **Database**: MySQL through Sail

## Common Commands
- PHP/Artisan: `sail artisan [command]`
- Composer: `sail composer [command]`
- Node packages: `sail pnpm [command]`
- Database: `sail mysql`
- Tests: `sail test`

## Development Workflow
1. Start containers: `sail up -d`
2. Run dev server: `sail pnpm run dev`
3. Build assets: `sail pnpm run build`
4. Stop containers: `sail down`

## Important Notes
- Never use `php artisan` directly, always use `sail artisan`
- Never use `npm`, always use `pnpm`
- All commands should be run through Sail container