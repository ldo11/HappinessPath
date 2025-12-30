import { test, expect, type Page, type Dialog } from '@playwright/test';

async function login(page: Page, email: string, password: string) {
  await page.goto('/login');
  await page.locator('#email').fill(email);
  await page.locator('#password').fill(password);
  await page.getByRole('button', { name: /login/i }).click();
}

test.describe('User flows', () => {
  test('Scenario 1: Admin Management', async ({ page }: { page: Page }) => {
    await login(page, 'admin@test.com', '123456');

    await page.goto('/admin/users');

    await page.getByRole('link', { name: /add user/i }).click();

    const email = `e2e-user-${Date.now()}@test.com`;

    await page.locator('#name').fill('E2E Created User');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('12345678');
    await page.locator('#password_confirmation').fill('12345678');
    await page.locator('#role').selectOption('user');
    await page.getByRole('button', { name: /create user/i }).click();

    await expect(page).toHaveURL(/\/admin\/users/);
    await expect(page.getByText(email)).toBeVisible();

    page.once('dialog', async (dialog: Dialog) => {
      await dialog.accept();
    });

    const userRow = page.locator('tr', { hasText: 'user@test.com' });
    await expect(userRow).toBeVisible();
    await userRow.getByRole('button', { name: /reset assessment/i }).click();

    await expect(page.getByText(/assessment reset/i)).toBeVisible();
  });

  test('Scenario 2: User Onboarding & Assessment', async ({ page }: { page: Page }) => {
    await login(page, 'user@test.com', '123456');

    await expect(page).toHaveURL(/\/assessment/);

    for (let i = 0; i < 30; i++) {
      const options = page.locator('#questionContainer input[type="radio"]');
      await expect(options.first()).toBeVisible();

      const count = await options.count();
      const choice = Math.floor(Math.random() * Math.max(count, 1));
      await options.nth(choice).click();
    }

    const submit = page.locator('#submitSection button[type="submit"]');
    await expect(submit).toBeVisible();
    await submit.click();

    await expect(page).toHaveURL(/\/dashboard/);

    await expect(page.locator('.fa-tree')).toBeVisible();
    await expect(page.getByRole('button', { name: /start practice/i })).toBeVisible();
  });

  test('Scenario 3: Volunteer Translation Workflow', async ({ page }: { page: Page }) => {
    await login(page, 'volunteer@test.com', '123456');

    await page.goto('/translator/translations');

    const rows = page.locator('tbody tr');
    await expect(rows.first()).toBeVisible();

    const initialCount = await rows.count();

    await rows
      .first()
      .getByRole('link', { name: /review/i })
      .click();

    await expect(page).toHaveURL(/\/translator\/translations\/.*\/review/);

    await page.locator('textarea[name="content"]').fill(`Approved by Playwright at ${Date.now()}`);
    await page.getByRole('button', { name: /approve/i }).click();

    await expect(page).toHaveURL(/\/translator\/translations/);

    const remainingCount = await page.locator('tbody tr').count();
    await expect(remainingCount).toBeLessThan(initialCount);
  });

  test('Scenario 4: User manages Pain Points and sees Top 3 on Dashboard', async ({ page }: { page: Page }) => {
    await login(page, 'pain-e2e@test.com', '123456');

    await expect(page).toHaveURL(/\/dashboard/);

    await page.getByRole('link', { name: 'Quản lý tất cả vấn đề' }).click();
    await expect(page).toHaveURL(/\/pain-points/);

    const painCard = page.locator('div', { hasText: 'Mất ngủ triền miên' }).first();
    await expect(painCard).toBeVisible();

    await painCard.locator('input[type="checkbox"]').check();
    await painCard.locator('input[type="range"]').fill('10');

    await page.getByRole('button', { name: /lưu/i }).click();

    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.getByText('Mất ngủ triền miên')).toBeVisible();
  });
});
