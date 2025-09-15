<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use File;
class MakeViewCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
   // Tạo một cái chữ ký, thằng nào gõ lệnh, php artisan make:view user.view chẳng hạn thì nó trả về user.view
  protected $signature = 'make:view {view}';
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new blade template.';
  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
  // gọi thằng $this tức là thằng MakeViewCommand và thực thi phương thức argumment của nó, cụ thể là thằng cha nó Illuminate\Console\Command, kết quả trả về là user.view
    $view = $this->argument('view');
// Gọi hàm viewPath, để tạo mọt cái đường dẫn gắn vô biến Path
    $path = $this->viewPath($view);
    $this->createDir($path);
    if (File::exists($path))
    {
        $this->error("File {$path} already exists!");
        return;
    }
    File::put($path, $path);
    $this->info("File {$path} created.");
  }
  /**
   * Get the view full path.
   *
   * @param string $view
   *
   * @return string
   */
  public function viewPath($view)
  {
    $view = str_replace('.', '/', $view) . '.blade.php';
    $path = "resources/views/{$view}";
    return $path;
  }
  /**
   * Create a view directory if it does not exist.
   *
   * @param $path
   */
  public function createDir($path)
  {
    $dir = dirname($path);
    if (!file_exists($dir))
    {
        mkdir($dir, 0777, true);
    }
  }
}