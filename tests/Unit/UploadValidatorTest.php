<?php

declare(strict_types=1);

namespace BuligAI\Tests\Unit;

use BuligAI\Modules\Files\Services\UploadValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UploadValidatorTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        $this->path = tempnam(sys_get_temp_dir(), 'buligai-');
    }

    protected function tearDown(): void
    {
        unlink($this->path);
    }

    public function testDetectsPdfContentWithoutTrustingBrowserMime(): void
    {
        file_put_contents($this->path, "%PDF-1.7\nA community guide\n%%EOF");
        $result = (new UploadValidator())->validate($this->path, UPLOAD_ERR_OK);
        self::assertSame('application/pdf', $result['mime']);
        self::assertSame('pdf', $result['extension']);
        self::assertSame(filesize($this->path), $result['size']);
    }

    public function testRejectsExecutableContentDisguisedAsAnImage(): void
    {
        file_put_contents($this->path, '<?php echo "unsafe";');
        $this->expectException(InvalidArgumentException::class);
        (new UploadValidator())->validate($this->path, UPLOAD_ERR_OK);
    }

    public function testRejectsFilesOverTenMebibytes(): void
    {
        $handle = fopen($this->path, 'wb');
        ftruncate($handle, 10 * 1024 * 1024 + 1);
        fclose($handle);
        $this->expectException(InvalidArgumentException::class);
        (new UploadValidator())->validate($this->path, UPLOAD_ERR_OK);
    }

    public function testRejectsIncompleteUploads(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new UploadValidator())->validate($this->path, UPLOAD_ERR_PARTIAL);
    }

    public function testRejectsEmptyFiles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new UploadValidator())->validate($this->path, UPLOAD_ERR_OK);
    }

    public function testVercelLimitRejectsAFileThatFitsTheLocalLimit(): void
    {
        $handle = fopen($this->path, 'wb');
        fwrite($handle, "%PDF-1.7\n");
        ftruncate($handle, 3 * 1024 * 1024 + 1);
        fclose($handle);
        $this->expectException(InvalidArgumentException::class);
        (new UploadValidator(3 * 1024 * 1024))->validate($this->path, UPLOAD_ERR_OK);
    }
}
