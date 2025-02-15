<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Filament\Resources\ProdukResource\RelationManagers;
use App\Models\Brand;
use App\Models\Kategori;
use App\Models\Produk;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Filament\Forms\Set;




class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $label = 'Produk';
    protected static ?string $pluralLabel = 'Produk';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Grid::make()
                        ->schema([
                            Select::make('kategori_id')
                                ->required()
                                ->options(function () {
                                    return Kategori::pluck('nama', 'id')->toArray();
                                })
                                ->placeholder('Pilih kategori')
                                ->searchable(),

                            Select::make('brand_id')
                                ->required()
                                ->options(function () {
                                    return Brand::pluck('nama', 'id')->toArray();
                                })
                                ->placeholder('Pilih brand')
                                ->searchable(),

                            TextInput::make('nama')
                                ->required()
                                ->maxLength(100)
                                ->live(onBlur:true)
                                ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create'  ? $set('slug', Str::slug($state)) :null),

                            TextInput::make('slug')
                                ->required()
                                ->maxLength(100)
                                ->disabled()
                                ->dehydrated()
                                ->unique(Produk::class, 'slug', ignoreRecord: true),

                            TextInput::make('harga')
                                ->required()
                                ->numeric()
                                ->prefix('Rp.'),

                            TextInput::make('stok')
                                ->numeric(),

                            TextInput::make('terjual')
                                ->numeric(),

                            TextInput::make('rating')
                                ->numeric()
                                ->default(0.00),

                            TextInput::make('berat')
                                ->required()
                                ->numeric()
                                ->default(0.00),

                            Textarea::make('deskripsi')
                                ->required()
                                ->columnSpanFull(),

                        ]),
                    FileUpload::make('images')
                        ->label('Gambar')
                        ->image()
                        ->multiple()
                        ->directory('produks'),

                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->required(),

                    Toggle::make('is_available')
                        ->label('Tersedia')
                        ->default(true)
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.nama')
                    ->label('Nama Brand')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('images')
                    ->label('Gambar'),

                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('terjual')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Tersedia')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('berat')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
