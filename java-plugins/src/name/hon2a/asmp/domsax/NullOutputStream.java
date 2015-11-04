package name.hon2a.asmp.domsax;

import java.io.IOException;
import java.io.OutputStream;

/**
 * This stream will suppress all output.
 */
class NullOutputStream extends OutputStream
{
    @Override
    public void write(int b) throws IOException {
        // Do nothing.
    }
}
