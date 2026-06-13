<?php
if (file_exists('debug_out.txt')) {
    unlink('debug_out.txt');
}
unlink('delete_txt.php');
echo "Cleanup completed successfully.";
