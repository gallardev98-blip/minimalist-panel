<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire;

use Panel\Minimalist\Livewire\Concerns\DispatchesPanelToasts;
use Panel\Minimalist\Livewire\Concerns\InteractsWithPanelResource;
use Panel\Minimalist\Support\PanelAuth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.app')]
final class Profile extends Component
{
    use DispatchesPanelToasts;
    use InteractsWithPanelResource;

    public string $name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        abort_unless(PanelAuth::profileEnabled(), 404);

        $user = PanelAuth::user();

        abort_if($user === null, 403);

        $this->name = (string) ($user->name ?? '');
        $this->email = (string) ($user->email ?? '');
    }

    public function save(): void
    {
        $user = PanelAuth::user();

        abort_if($user === null, 403);

        $validated = $this->validate($this->rules($user));

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($validated['password'] ?? null) {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->toastSuccess(__('panel::panel.profile.saved'));
    }

    public function render(): mixed
    {
        return view('panel::livewire.profile', $this->sharedPanelData())
            ->title(__('panel::panel.profile.title'));
    }

    /** @return array<string, mixed> */
    private function rules(Authenticatable $user): array
    {
        if (! $user instanceof \Illuminate\Database\Eloquent\Model) {
            throw ValidationException::withMessages([
                'email' => __('panel::panel.profile.unsupported_user'),
            ]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique($user->getTable(), 'email')->ignore($user->getKey()),
            ],
            'password' => ['nullable', 'string', 'confirmed', PasswordRule::defaults()],
            'current_password' => ['nullable', 'string'],
        ];

        if ($this->password !== '') {
            $rules['current_password'] = ['required', 'current_password:'.PanelAuth::guard()];
        }

        return $rules;
    }
}
