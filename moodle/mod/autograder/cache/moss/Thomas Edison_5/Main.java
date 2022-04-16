/*
	Copyright @Thomas Edison
	Direct Current > Alternative Current
	thomasedison@gmail.com for pricing
*/

public class Main {
	
	public static void main(String[] args) {
		if(args.length != 5) {
			System.exit(0);
		}
		int lengthFirst = Integer.parseInt(args[0]);
		int amountFirst = Integer.parseInt(args[1]);
		int lengthSecond = Integer.parseInt(args[2]);
		int amountSecond = Integer.parseInt(args[3]);
		int lengthTarget = Integer.parseInt(args[4]);
		if((lengthFirst * amountFirst + lengthSecond * amountSecond) < lengthTarget) {
			System.out.println(false);
			return;
		}
		for(int i = 0; i <= amountFirst; i++) {
			if((i * lengthFirst + amountSecond * lengthSecond) < lengthTarget
			||	lengthTarget - i * lengthFirst < 0) {
				continue;
			}
			if((lengthTarget - i * lengthFirst) % lengthSecond == 0) {
				System.out.println(true);
				return;
			}
		}
		System.out.println(false);
	}
	
}