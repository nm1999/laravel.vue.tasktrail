<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrganizationRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register_an_organization_and_become_its_admin(): void
    {
        $response = $this->post('/register', [
            'account_type' => 'organization',
            'organization_name' => 'Northwind Labs',
            'firstname' => 'Avery',
            'surname' => 'Stone',
            'email' => 'avery@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();

        $owner = User::where('email', 'avery@example.test')->firstOrFail();
        $this->assertSame('admin', $owner->roles()->firstOrFail()->role);
        $this->assertSame('Northwind Labs', $owner->workspace->name);
        $this->assertTrue(Str::isUuid($owner->workspace->public_id));
    }

    public function test_multiple_organizations_can_register_independently(): void
    {
        $this->post('/register', [
            'account_type' => 'organization',
            'organization_name' => 'Northwind Labs',
            'firstname' => 'Avery',
            'surname' => 'Stone',
            'email' => 'avery@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/admin/dashboard');

        $this->post('/logout');

        $this->post('/register', [
            'account_type' => 'organization',
            'organization_name' => 'Contoso',
            'firstname' => 'Jordan',
            'surname' => 'Lee',
            'email' => 'jordan@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/admin/dashboard');

        $this->assertDatabaseCount('workspaces', 2);
        $this->assertNotSame(
            User::where('email', 'avery@example.test')->firstOrFail()->workspace->public_id,
            User::where('email', 'jordan@example.test')->firstOrFail()->workspace->public_id,
        );
    }

    public function test_an_employee_can_register_under_an_organization_id(): void
    {
        $workspace = $this->createOrganization('Northwind Labs');

        $response = $this->post('/register', [
            'account_type' => 'employee',
            'organization_id' => $workspace->public_id,
            'firstname' => 'Jordan',
            'surname' => 'Lee',
            'email' => 'jordan@example.test',
            'department' => 'Engineering',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/employee/dashboard');
        $employee = User::where('email', 'jordan@example.test')->firstOrFail();
        $this->assertSame($workspace->id, $employee->workspace_id);
        $this->assertSame('employee', $employee->roles()->firstOrFail()->role);
    }

    public function test_guests_can_contact_an_organization_by_its_id_and_messages_are_scoped(): void
    {
        $workspace = $this->createOrganization('Northwind Labs');
        $otherWorkspace = $this->createOrganization('Contoso');

        $this->get('/organizations/contact')->assertOk();

        $this->post('/organizations/contact', [
            'organization_id' => $workspace->public_id,
            'name' => 'Guest Person',
            'email' => 'guest@example.test',
            'message' => 'I would like to learn more.',
        ])->assertRedirect(route('organizations.contact'));

        $this->post('/organizations/contact', [
            'organization_id' => $otherWorkspace->public_id,
            'name' => 'Other Guest',
            'email' => 'other@example.test',
            'message' => 'A private message.',
        ])->assertRedirect(route('organizations.contact'));

        $this->assertDatabaseHas('contact_messages', [
            'workspace_id' => $workspace->id,
            'email' => 'guest@example.test',
        ]);

        $this->actingAs($workspace->owner)->get('/admin/contact-messages')
            ->assertOk()
            ->assertSee('guest@example.test')
            ->assertDontSee('other@example.test');
    }

    public function test_an_administrator_only_sees_employees_from_their_organization(): void
    {
        $workspace = $this->createOrganization('Northwind Labs');
        $otherWorkspace = $this->createOrganization('Contoso');
        $employee = $this->createEmployee($workspace, 'local@example.test');
        $otherEmployee = $this->createEmployee($otherWorkspace, 'foreign@example.test');

        $this->actingAs($workspace->owner)->get('/admin/employees')
            ->assertOk()
            ->assertSee($employee->email)
            ->assertDontSee($otherEmployee->email);
    }

    private function createOrganization(string $name): Workspace
    {
        $owner = User::create([
            'firstname' => 'Org',
            'surname' => 'Admin',
            'email' => Str::slug($name).'@example.test',
            'password' => 'password123',
        ]);

        $workspace = Workspace::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(8),
            'public_id' => (string) Str::uuid(),
            'user_id' => $owner->id,
        ]);

        $owner->workspace()->associate($workspace);
        $owner->save();
        UserRole::create(['user_id' => $owner->id, 'role' => 'admin']);

        return $workspace->load('owner');
    }

    private function createEmployee(Workspace $workspace, string $email): User
    {
        $employee = User::create([
            'firstname' => 'Team',
            'surname' => 'Member',
            'email' => $email,
            'password' => 'password123',
            'workspace_id' => $workspace->id,
        ]);

        UserRole::create(['user_id' => $employee->id, 'role' => 'employee']);

        return $employee;
    }
}
