import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<div { ...blockProps }>
				<section className="rehab-stat-band">
					<div className="rehab-container">
						<div className="rehab-stat-band__grid">
							{ [ 1, 2, 3, 4 ].map( ( i ) => (
								<div className="rehab-stat-band__item" key={ i }>
									<RichText tagName="div" className="rehab-stat-band__value" value={ a[ `stat${ i }Num` ] } onChange={ set( `stat${ i }Num` ) } placeholder="N" allowedFormats={ [ 'core/italic' ] } />
									<RichText tagName="div" className="rehab-stat-band__label" value={ a[ `stat${ i }Label` ] } onChange={ set( `stat${ i }Label` ) } placeholder="Label…" allowedFormats={ [] } />
								</div>
							) ) }
						</div>
					</div>
				</section>
			</div>
		);
	},
	save: () => null,
} );
