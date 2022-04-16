public class Main {
	
	private static boolean is_buildable(int len_first, int amt_first, int len_second, int amt_second, int len_target) {
		if(len_target == 0 && amt_first >= 0 && amt_second >= 0) {
			return true;
		}
		if(len_target < 0 || amt_first < 0 || amt_second < 0) {
			return false;
		}
		return is_buildable(len_first, amt_first-1, len_second, amt_second, len_target-len_first)
			|| is_buildable(len_first, amt_first, len_second, amt_second-1, len_target-len_second);
	}
	
	public static void main(String[] args) {
		if(args.length != 5) {
			System.exit(0);
		}
		int len_first = Integer.parseInt(args[0]);
		int amt_first = Integer.parseInt(args[1]);
		int len_second = Integer.parseInt(args[2]);
		int amt_second = Integer.parseInt(args[3]);
		int len_target = Integer.parseInt(args[4]);
		if((len_first * amt_first + len_second * amt_second) < len_target) {
			System.out.println(false);
		}
		else {
			System.out.println(is_buildable(len_first, amt_first, len_second, amt_second, len_target));
		}
	}
	
}