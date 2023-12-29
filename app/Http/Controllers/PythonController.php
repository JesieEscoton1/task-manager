<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PythonController extends Controller
{
    public function executeScript(Request $request)
    {
        $scriptPath = base_path('python_scripts/python.py');
        $pythonExecutable = 'C:\\Program Files\\Python312\\python.exe';
        try {
            $process = new Process([$pythonExecutable, $scriptPath]);
            $process->setWorkingDirectory(dirname($scriptPath));
            $process->run();
    
            $output = $process->getOutput();
    
            if ($process->isSuccessful()) {
                return view('python-script.index', ['output' => $output]);
            } else {
                throw new ProcessFailedException($process);
            }
        } catch (ProcessFailedException $exception) {
            $error = 'Error executing Python script: ' . $exception->getMessage();
            $error .= "\n\n" . $process->getErrorOutput();
            return view('python-script.index', ['error' => $error]);
        }
    }

    public function runPython()
    {
        return view('python-script.index');
    }
}
