<?php

namespace Nphuonha\FilamentHelpdesk\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Nphuonha\FilamentHelpdesk\Models\Ticket;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SubmitTicket extends Component implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(fn () => !Auth::check())
                    ->hidden(fn () => Auth::check())
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('priority')
                    ->options(\Nphuonha\FilamentHelpdesk\Enums\TicketPriority::class)
                    ->default(\Nphuonha\FilamentHelpdesk\Enums\TicketPriority::Normal)
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->rows(5),
                Forms\Components\FileUpload::make('attachments')
                    ->multiple()
                    ->maxFiles(5),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $ticketData = [
            'uuid' => (string) Str::uuid(),
            'subject' => $data['subject'],
            'priority' => $data['priority'],
            'status' => \Nphuonha\FilamentHelpdesk\Enums\TicketStatus::Open,
        ];

        if (Auth::check()) {
            $ticketData['user_id'] = Auth::id();
            $ticketData['email'] = Auth::user()->email;
        } else {
            $ticketData['email'] = $data['email'];
        }

        $ticket = Ticket::create($ticketData);

        $ticket->messages()->create([
            'body' => $data['message'],
            'attachments' => $data['attachments'] ?? [],
            'user_id' => Auth::id(), // Null if guest
        ]);

        $this->form->fill();
        
        session()->flash('message', 'Ticket submitted successfully!');
    }

    public function render()
    {
        return view('filament-helpdesk::livewire.submit-ticket');
    }
}
